<?php

namespace App\Console\Commands\Book;

use App\BookTextProcessing;
use App\Jobs\Book\BookUpdatePageNumbersJob;
use App\Notifications\BookTextProcessingCompleteNotification;
use App\Section;
use DOMCharacterData;
use DOMDocument;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Stevebauman\Purify\Facades\Purify;

class BookTextWaitedProcessingCommand extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'book:text_waited_processing {latest_id=0}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Комманда ищет книги, которые ожидают обработки текста и выполняет их';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}

	private $sections;
	public $isMustUpperCaseFixParam;

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle()
	{
		BookTextProcessing::waited()
			->with(['book.sections' => function ($query) {
				$query->chaptersOrNotes();
			}])
			->where('id', '>=', $this->argument('latest_id'))
			->chunkById(10, function ($items) {

				$items->load('book.sections.pages');

				foreach ($items as $item) {
					$this->item($item);
				}
			});
	}

	public function item(BookTextProcessing $item)
	{
		if (!$item->isWait())
			return false;

		if (empty($item->book))
			return false;

		if (!$item->book->isPagesNewFormat())
			return false;

		$item->start();
		$item->save();

		DB::transaction(function () use ($item) {
			$this->book($item);
		});

		$item->book->forbid_to_change = false;
		$item->book->need_create_new_files = true;
		$item->book->save();
		$item->complete();
		$item->save();

		$item->create_user->notify(new BookTextProcessingCompleteNotification($item));
	}

	public function book(BookTextProcessing $item)
	{
		$this->sections = clone $item->book->sections()->get();
		$this->isMustUpperCaseFixParam = $this->isMustUpperCaseFix();

		foreach ($this->sections as $section) {
			$this->section($item, $section);
		}

		BookUpdatePageNumbersJob::dispatch($item->book);

		$this->sections = [];
	}

	public function section(BookTextProcessing $item, Section $section)
	{
		if (!$section->isNote() and !$section->isChapter())
			return false;

		$section->content = $section->getContent();

		if ($item->convert_new_lines_to_paragraphs) {
			$section->content = $this->brToParagraph($section->saveXML());
		}

		if ($item->remove_bold) {
			$section->content = $this->removeStrongTag($section->saveXML());
		}

		if ($item->remove_italics) {
			$section->content = $this->removeItalicTag($section->saveXML());
		}

		if ($item->remove_spaces_before_punctuations_marks) {
			$section->content = $this->removeSpacesBeforePunctuationMarks($section->saveXML());
		}

		if ($item->add_spaces_after_punctuations_marks) {
			$section->content = $this->addSpacesAfterPunctuationsMarks($section->saveXML());
		}

		if ($item->merge_paragraphs_if_there_is_no_dot_at_the_end)
			$section->content = $this->mergeParagraphsIfThereIsNoDotAtTheEnd($section->saveXML());

		if ($item->remove_extra_spaces) {
			foreach ($section->dom()->getElementsByTagName('body')->item(0)->childNodes as $node) {
				if ($node->tagName == 'p') {
					foreach ($node->childNodes as $childNode) {
						if ($childNode instanceof DOMCharacterData) {
							$value = $childNode->nodeValue;
							$previousValue = $value;

							$value = $this->removeExtraSpacesBeforeTheTextInTheParagraph($value);

							if ($previousValue != $value) {
								$childNode->nodeValue = $value;
							}
						}
					}
				}
			}
		}

		if ($item->remove_empty_paragraphs) {
			$section = $this->removeEmptyParagraphs($section);
		}

		if ($item->add_a_space_after_the_first_hyphen_in_the_paragraph)
			$this->addASpaceAfterTheFirstHyphenInTheParagraph($section);

		if ($item->tidy_chapter_names)
			$section->title = $this->tidyChapterTitle($section->title);

		$section->save();
		$section->refresh();

		if ($item->split_into_chapters) {

			$this->splitIntoChapters($item, $section, $section->getContent());
		}
	}

	public function brToParagraph($content)
	{
		$content = mb_str_replace('<br>', '</p><p>', $content);
		$content = mb_str_replace('<br/>', '</p><p>', $content);

		$section = new Section;
		$configuration = $section->getPurifyConfig();
		$content = Purify::clean($content, array_merge($configuration, ['AutoFormat.RemoveEmpty' => 'true']));

		return $content;
	}

	public function removeStrongTag($content)
	{
		$section = new Section;
		$configuration = $section->getPurifyConfig();
		$content = Purify::clean($content, array_merge($configuration, ['HTML.ForbiddenElements' => 'strong,b']));
		return $content;
	}

	public function removeItalicTag($content)
	{
		$section = new Section;
		$configuration = $section->getPurifyConfig();
		$content = Purify::clean($content, array_merge($configuration, ['HTML.ForbiddenElements' => 'em,i']));
		return $content;
	}

	public function removeSpacesBeforePunctuationMarks($content)
	{
		$content = preg_replace_callback('/([[:alnum:]]+)([[:space:]]*)([\,\.\;\:\?\!]+)/iu', function ($matches) {
			return $matches[1] . $matches[3];
		}, $content);

		return $content;
	}

	public function addSpacesAfterPunctuationsMarks($content)
	{
		$content = preg_replace_callback('/([[:alpha:]]+)([\,\.\;\:\?\!\…]+)([[:space:]]*)/iu', function ($matches) {
			return $matches[1] . $matches[2] . ' ';
		}, $content);

		return $content;
	}

	public function mergeParagraphsIfThereIsNoDotAtTheEnd($content)
	{
		$content = preg_replace_callback('/([^\.\?\![:space:]]+)([[:space:]]*)\<\/p\>([[:space:]]*)\<p\>/iuU', function ($matches) {
			return $matches[1] . ' ';
		}, $content);

		return $content;
	}

	public function addASpaceAfterTheFirstHyphenInTheParagraph(Section $section)
	{
		foreach ($section->dom()->getElementsByTagName('body')->item(0)->childNodes as $node) {
			if ($node->tagName == 'p') {
				foreach ($node->childNodes as $childNode) {
					if ($childNode instanceof DOMCharacterData) {
						$value = $childNode->nodeValue;
						$previousValue = $value;

						if (preg_match('/^([[:space:]]*)(\-|\—)(.*)/iu', $value, $matches)) {
							$childNode->nodeValue = $matches[1] . $matches[2] . ' ' . $matches[3];
						}
					}
				}
			}
		}
	}

	public function splitIntoChapters(BookTextProcessing $item, Section $section, $content)
	{
		$dom = new DOMDocument();
		$dom->loadHTML('<meta http-equiv="Content-Type" content="text/html; charset=utf-8">' . $content);

		$body = $dom->getElementsByTagName('body')->item(0);

		if (!empty($body->childNodes) and $body->childNodes->length > 0) {
			$n = 1;
			$array = [];

			foreach ($body->childNodes as $node) {
				$title = $node->nodeValue;

				if (empty($array[$n]['content'])) {
					$array[$n]['content'] = '';
				}

				if ($this->isSectionTitle($title)) {
					$n++;
					$array[$n]['title'] = $title;
					$array[$n]['title_id'] = $node->getAttribute('id');
				} else {
					$array[$n]['content'] .= $dom->saveXML($node);
				}
			}

			$lastSection = $section;

			foreach ($array as $number => $value) {
				if ($number == 1) {
					if ($content != $array[$number]['content']) {
						$section->content = $array[$number]['content'];
						$section->save();
					}
				} else {
					$newSection = new Section;
					$newSection->scoped(['book_id' => $item->book->id, 'type' => $section->type]);
					$newSection->title = $array[$number]['title'];

					if (!empty($array[$number]['content']))
						$newSection->content = $array[$number]['content'];

					$newSection->book_id = $item->book->id;
					$newSection->user_edited_at = now();
					$newSection->setTitleId($array[$number]['title_id']);
					$newSection->afterNode($lastSection)->save();
					$lastSection = $newSection;
				}
			}
		}
	}

	public function isSectionTitle($string)
	{
		$string = preg_replace('/(\&nbsp\;)+/iu', ' ', $string);
		$string = preg_replace('/^\s+/iu', ' ', $string);
		$string = trim($string);

		if (preg_match('/^(?:Глава|Часть)([[:space:]]{1,})([0-9]+)$/iu', $string))
			return true;

		if (preg_match('/^(Эпилог|Предисловие|Пролог)$/iu', $string))
			return true;

		if (preg_match('/^([0-9]+)([[:space:]]{1,})Глава$/iu', $string))
			return true;

		if (preg_match('/^(?:Глава|Часть)([[:space:]]{1,})([0-9]+)(.{1,200})$/iu', $string))
			return true;

		if (preg_match('/^(?:Пролог)(?:\.?)([[:space:]]{1,})(Часть)([[:space:]]{1,})([0-9]+)$/iu', $string))
			return true;

		$array = [
			'первая',
			'вторая',
			'третья',
			'четвертая',
			'пятая',
			'шестая',
			'седьмая',
			'восьмая',
			'девятая',
			'десятая',
			'одиннадцатая',
			'двенадцатая',
			'тринадцатая',
			'четырнадцатая',
			'пятнадцатая',
			'шестнадцатая',
			'семнадцатая',
			'восемнадцатая',
			'девятнадцатая',
			'двадцатая',
			'тридцатая',
			'сороковая',
			'пятидесятая',
			'шестидесятая',
			'семидесятая',
			'восмидесятая',
			'девяностая',
			'сотая',
			'четвёртая'
		];

		if (preg_match('/^(?:Глава|Часть)([[:space:]]{1,})(.{0,30})(' . implode('|', $array) . ')(.{0,200})$/iu', $string))
			return true;

		if (preg_match('/^(?:Глава|Часть)([[:space:]]{1,})([XCDIVLM]+)(.{0,200})$/iu', $string, $matches)) {
			if (!empty($matches[2])) {
				if (strripos($matches[3], ' ') > 0 or (empty($matches[3]))) {
					if ($this->convertRomanNumberToInteger($matches[2]) > 0)
						return true;
				}
			}
		}

		if (preg_match('/^([0-9]+)\.([[:space:]]+)([[:upper:]]{0,100})$/iu', $string))
			return true;

		return false;
	}

	public function convertRomanNumberToInteger(string $roman): int
	{
		$romans = [
			'M' => 1000,
			'CM' => 900,
			'D' => 500,
			'CD' => 400,
			'C' => 100,
			'XC' => 90,
			'L' => 50,
			'XL' => 40,
			'X' => 10,
			'IX' => 9,
			'V' => 5,
			'IV' => 4,
			'I' => 1,
		];

		$result = 0;

		foreach ($romans as $key => $value) {
			while (strpos($roman, $key) === 0) {
				$result += $value;
				$roman = substr($roman, strlen($key));
			}
		}

		return $result;
	}

	public function tidyChapterTitle(string $title): string
	{
		$title = trim($title);

		if ($this->isMustUpperCaseFixParam) {

			$array = explode('.', $title);

			foreach ($array as $key => $value) {

				$array[$key] = preg_replace_callback('/([[:space:]]*)([[:print:]]+)/iu', function ($mathches) {
					return $mathches[1] . mb_ucfirst(mb_strtolower($mathches[2]));
				}, $value);
			}

			$title = implode('.', $array);
		}

		$title = preg_replace('/([[:space:]]+)/iu', ' ', $title);

		$title = preg_replace('/глава(\.*)/iu', 'Глава', $title);

		$title = preg_replace_callback('/([0-9]+)([[:space:]]+)(глава)(\.*)$/iu', function ($mathches) {
			if (mb_strlen($mathches[4]) > 1)
				return 'Глава ' . $mathches[1] . $mathches[4];
			else
				return 'Глава ' . $mathches[1];
		}, $title);

		$title = preg_replace_callback('/(глава)([[:space:]]+)([0-9]+)(\.*)$/iu', function ($mathches) {
			if (mb_strlen($mathches[4]) > 1)
				return 'Глава ' . $mathches[3] . $mathches[4];
			else
				return 'Глава ' . $mathches[3];
		}, $title);

		return $title;
	}

	public function isMustUpperCaseFix(): bool
	{
		$str = implode('', $this->sections->pluck('title')->toArray());

		if ($this->getLettersCount($str) > 5) {
			if ($this->getUpperCaseLettersPercent($str) > 80) {
				return true;
			}
		}

		return false;
	}

	public function getUpperCaseLettersPercent(string $string): int
	{
		$lettersCount = $this->getLettersCount($string);

		if ($lettersCount < 1)
			return 0;

		return ($this->getUpperLettersCount($string) * 100) / $lettersCount;
	}

	public function getLettersCount(string $string): int
	{
		preg_match_all('/([[:alpha:]])/u', $string, $matches);

		return count($matches[1]);
	}

	public function getUpperLettersCount(string $string): int
	{
		preg_match_all('/([[:upper:]])/u', $string, $matches);

		return count($matches[1]);
	}

	public function getLowerLettersCount(string $string): int
	{
		preg_match_all('/([[:lower:]])/u', $string, $matches);

		return count($matches[1]);
	}

	public function removeExtraSpacesBeforeTheTextInTheParagraph($value)
	{
		$value = preg_replace('/^(\&nbsp\;)+/iu', ' ', $value);

		$chr226 = '⠀';

		$value = preg_replace('/([[:space:]]|' . $chr226 . ')+/iu', ' ', $value);

		return $value;
	}

	public function removeEmptyParagraphs(Section $section)
	{
		$content = '';

		foreach ($section->dom()->getElementsByTagName('body')->item(0)->childNodes as $node) {

			if (in_array($node->tagName, ['div', 'p'])) {

				if (trim($node->nodeValue) != '' or $section->xpath()->query('.//*[local-name()=\'img\']', $node)->count() > 0) {
					$content .= $section->dom()->saveXML($node);
				}
			}
		}

		$section->content = $content;

		return $section;
	}
}
