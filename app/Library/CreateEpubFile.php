<?php

namespace App\Library;

use App\Book;
use App\Section;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Litlife\Epub\Css;
use Litlife\Epub\Epub;
use Litlife\Epub\Image;
use Litlife\Url\Exceptions\InvalidArgument;
use Litlife\Url\Url;

class CreateEpubFile
{
	private $epub;
	private $book;
	private $point_number = 0;

	public function __construct()
	{
		$this->epub = new Epub();
	}

	function setBookid($id)
	{
		$this->book = Book::any()->findOrFail($id);
	}

	public function init()
	{
		$this->epub->createContainer();
		$this->epub->createOpf();

		$this->addOpf();
		$this->addNcx();
		$this->addAttachements();
		$this->addSections();
		$this->addStyles();
	}

	private function addOpf()
	{
		$opf = $this->epub->opf();

		$opf->package()->setAttribute('unique-identifier', 'LitlifeBookUrl');
		$opf->appendDublinCode('identifier', route('books.show', ['book' => $this->book->id]), ['id' => 'LitlifeBookUrl', 'opf:scheme' => 'URI']);

		$opf->appendDublinCode('title', $this->book->title);
		$opf->appendDublinCode('language', $this->book->ti_lb, ['xsi:type' => "dcterms:RFC3066"]);

		$authors = $this->book->writers()
			->when($this->book->isPrivate() and isset($this->book->create_user), function ($query) {
				$query->checkedOrBelongsToUser($this->book->create_user);
			})
			->get();

		// авторы книги
		if (count($authors) > 0) {
			foreach ($authors as $c => $author) {
				$opf->appendDublinCode('creator', $author->FullName,
					['opf:role' => "aut", 'opf:file-as' => $author->FullName]);
			}
		}

		$translators = $this->book->translators()
			->when($this->book->isPrivate() and isset($this->book->create_user), function ($query) {
				$query->checkedOrBelongsToUser($this->book->create_user);
			})
			->get();

		// авторы книги
		if (count($translators) > 0) {
			foreach ($translators as $c => $translator) {
				$opf->appendDublinCode('contributor', $translator->FullName,
					['opf:role' => "trl", 'opf:file-as' => $translator->FullName]);
			}
		}

		// жанры книги
		if (count($genres = $this->book->genres()->get()) > 0) {
			foreach ($genres as $c => $genre) {
				$this->epub->opf()->appendDublinCode('subject', $genre->fb_code);
			}
		}

		if (isset($this->book->year_writing)) {
			$opf->appendDublinCode('created', $this->book->year_writing);
		}

		if (isset($this->book->cover->name)) {
			$opf->appendToMetaData('cover', $this->book->cover->name);
		}

		$sequences = $this->book->sequences()
			->when($this->book->isPrivate() and isset($this->book->create_user), function ($query) {
				$query->checkedOrBelongsToUser($this->book->create_user);
			})->get();

		// серии книги
		if (count($sequences) > 0) {
			foreach ($sequences as $c => $sequence) {
				$opf->appendToMetaData('calibre:series', $sequence->name);
				$opf->appendToMetaData('calibre:series_index', $sequence->pivot->number);
			}
		}

		$opf->appendToMetaData('calibre:title_sort', $this->book->title);

		// аннтоация
		$annotation = $this->book->annotation;
		if (!empty($annotation)) {
			$content = trim(strip_tags($annotation->getContent()));
			if (!empty($content)) {
				$opf->appendDublinCode('description', $content);
			}
		}

		// издатель
		if (!empty($this->book->pi_pub)) {
			$opf->appendDublinCode('publisher', $this->book->pi_pub);
		}

		// год публикации книги
		if (!empty($this->book->pi_year)) {
			//$opf->appendDublinCode('issued', $this->book->pi_year);
			$opf->appendDublinCode('date', $this->book->pi_year . '-01-01', ['opf:event' => 'original-publication']);
		}

		// правообладатель
		if (!empty($this->book->rightholder)) {
			$opf->appendDublinCode('rightsholder', $this->book->rightholder);
		}

		// isbn
		if (!empty($this->book->pi_isbn)) {
			$opf->appendDublinCode('identifier', 'urn:isbn:' . $this->book->pi_isbn, ['id' => 'pub-identifier']);
		}

		if (!empty($this->book->pi_city)) {
			$opf->appendToMetaData('FB2.publish-info.city', $this->book->pi_city);
		}

		if (!empty($this->book->pi_year)) {
			$opf->appendToMetaData('FB2.publish-info.year', $this->book->pi_year);
		}

		$opf->appendDublinCode('date', Carbon::now()->format('Y-m-d'), [
			'opf:event' => 'modification',
			'xmlns:opf' => 'http://www.idpf.org/2007/opf'
		]);
	}

	private function addNcx()
	{
		$this->epub->createNcx();

		$head = $this->epub->ncx()->head();
		/*
				$meta = $this->epub->ncx()->dom()->createElement('meta');
				$meta->setAttribute('content', "b271d443-dc22-4358-a461-79a1941eec0d");
				$meta->setAttribute('name', "dtb:uid");
				$head->appendChild($meta);
		*/
		$meta = $this->epub->ncx()->dom()->createElement('meta');
		$meta->setAttribute('content', "2");
		$meta->setAttribute('name', "dtb:depth");
		$head->appendChild($meta);

		$meta = $this->epub->ncx()->dom()->createElement('meta');
		$meta->setAttribute('content', "0");
		$meta->setAttribute('name', "dtb:totalPageCount");
		$head->appendChild($meta);

		$meta = $this->epub->ncx()->dom()->createElement('meta');
		$meta->setAttribute('content', "0");
		$meta->setAttribute('name', "dtb:maxPageNumber");
		$head->appendChild($meta);

		$this->addNcxSections();
	}

	private function addNcxSections()
	{
		$ncx = $this->epub->ncx();

		$this->point_number = 0;

		$sections = Section::scoped(['book_id' => $this->book->id, 'type' => 'section'])
			->defaultOrder()
			->get()
			->toTree();

		foreach ($sections as $section) {

			$this->addNcxSection($section);
		}
	}

	private function addNcxSection($section, $parentPoint = null)
	{
		$this->point_number++;

		$ncx = $this->epub->ncx();

		$name = $section->type . '_' . $section->inner_id . '.xhtml';

		$point = $ncx->appendNavMap($section->title,
			'Text/' . $name,
			"NavPoint-" . $section->getSectionId(),
			$this->point_number);

		foreach ($section->children as $child_section) {
			$this->addNcxSection($child_section, $parentPoint);
		}
	}

	private function addAttachements()
	{
		$attachments = $this->book->attachments()->where("type", "image")->get();

		$manifest = $this->epub->opf()->manifest();

		foreach ($attachments as $attachment) {

			if ($attachment->isExists()) {

				$epub_section = new Image($this->epub);
				$epub_section->setPath($this->getOebpsPath() . '/Images/' . $attachment->name);
				$epub_section->setContent($attachment->getContents());

				$this->epub->opf()->appendToManifest(
					$attachment->name,
					'Images/' . $attachment->name,
					$attachment->content_type
				);
			}
		}
	}

	private function getOebpsPath()
	{
		return 'OEBPS';
	}

	private function addSections()
	{
		$collection = new Collection;

		foreach (['section', 'note'] as $type) {
			$FlatTree = Section::scoped(['book_id' => $this->book->id, 'type' => $type])
				->defaultOrder()
				->get()
				->toFlatTree();

			$collection = $collection->merge($FlatTree);
		}

		$manifest = $this->epub->opf()->manifest();

		$spine = $this->epub->opf()->spine();

		foreach ($collection as $section) {

			$name = $section->type . '_' . $section->inner_id . '.xhtml';

			$epub_section = new \Litlife\Epub\Section($this->epub);
			$epub_section->title($section->title);
			$epub_section->setPath($this->getOebpsPath() . '/Text/' . $name);
			$epub_section->setBodyHtml($section->getContent());
			$epub_section->setBodyId($section->getSectionId());

			$this->handleSection($epub_section, $section);

			$this->epub->opf()->appendToManifest($name, 'Text/' . $name, 'application/xhtml+xml');
			$this->epub->opf()->appendToSpine($name);
		}
	}

	private function handleSection(\Litlife\Epub\Section &$epub_section, &$section)
	{
		$parentNode = $epub_section->dom()->getElementsByTagName('body')->item(0);
		$nodeList = $epub_section->xpath()->query("//img", $parentNode);

		$epub_section->prependBodyXhtml('<h1 class="title">' . htmlspecialchars($section->title) . '</h1>');

		if ($nodeList->length) {
			foreach ($nodeList as $node) {

				$src = Url::fromString($node->getAttribute("src"));

				$founded_attachment = $section->book->attachments->first(function ($attachment) use ($src) {

					$attachment_url = Url::fromString($attachment->url);

					if (
						($src->getHost() == $attachment_url->getHost()) and
						($src->getDirname() == $attachment_url->getDirname()) and
						($src->getBasename() == $attachment_url->getBasename())
					) {
						return $attachment;
					}
				});

				if (!empty($founded_attachment)) {
					$node->setAttribute("src", '../Images/' . $founded_attachment->name);
				} else {
					$node->setAttribute("src", $src);
				}
			}
		}

		$nodeList = $epub_section->xpath()->query("//a[@href]", $parentNode);

		if ($nodeList->length) {

			$fragments = [];

			foreach ($nodeList as $node) {

				$url = Url::fromString($node->getAttribute('href'));

				if ($url->getPath() == '/away' and !empty($url->getQueryParameter('url'))) {
					$node->setAttribute('href', $url->getQueryParameter('url'));
				}

				if (!$url->getHost()) {
					$fragments[] = $url->getFragment();
				}
			}

			if (!empty($fragments)) {
				$pages = $this->book->pages()->inLinksIdSections($fragments)
					->select('id', 'section_id', 'page', 'html_tags_ids', 'book_id')
					->with('section')
					->get();

				foreach ($nodeList as $node) {

					try {
						$url = Url::fromString($node->getAttribute('href'));

						if (!$url->getHost()) {

							$fragment = $url->getFragment();

							foreach ($pages as $page) {
								if (in_array($fragment, $page->html_tags_ids)) {
									if (!empty($page->section)) {
										$node->setAttribute("href", '../Text/' . $page->section->type . '_' . $page->section->inner_id . '.xhtml' . '#' . $fragment);
									}
								}
							}
						}

					} catch (InvalidArgument $exception) {

					}
				}
			}
		}

		/*
		$value = $section->content;

		preg_match_all('/\<a\ (?:.*)href(?:.*)\=(?:.*)\"(.*)\"(?:.*)\>/iuU', $value, $m);

		$urls = $m[1];

		foreach ($urls as $url) {
			$fragments[] = Url::fromString($url)->getFragment();
		}

		if (!empty($fragments)) {
			$pages = Page::inLinksIdSections($fragments)
				->select('id', 'section_id', 'page', 'html_tags_ids', 'book_id')
				->with('section')
				->where('book_id', $section->book_id)
				->get();

			$sections = Section::parametersIn('id', $fragments)
				->where('book_id', $section->book_id)
				->get();

			$value = preg_replace_callback('/\<a\ (.*)href(.*)\=(.*)\"(.*)\"(.*)\>/iuU', function ($m) use (&$pages, &$sections) {

				$fragment = Url::fromString($m[4])->getFragment();

				foreach ($pages as $page) {

					if (in_array($fragment, $page->html_tags_ids ?? [])) {

						$url = route('books.sections.show', [
							'book' => $page->book_id,
							'section' => $page->section->inner_id,
							'page' => $page->page,
							$fragment ? '#' . $fragment : ''
						]);

						$str = '<a ' . $m[1] . 'href' . $m[2] . '=' . $m[3] . '"' . $url . '"' . $m[5] . '>';

						return $str;
					}
				}

				return '<a ' . $m[1] . 'href' . $m[2] . '=' . $m[3] . '"' . $m[4] . '"' . $m[5] . '>';

			}, $value);
		}

		return $value;
		*/
	}

	private function addStyles()
	{
		$css = new Css($this->epub);
		$css->setContent(file_get_contents(public_path() . '/assets/css/styles_for_epub_books.css'));
		$css->setPath($this->getOebpsPath() . '/Styles/main.css');

		$this->epub->opf()->appendToManifest('main.css', 'Styles/main.css', 'text/css');
	}

	public function getEpub()
	{
		return $this->epub;
	}
}

?>