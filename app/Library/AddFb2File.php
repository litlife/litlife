<?php

namespace App\Library;

use App\Attachment;
use App\Author;
use App\Book;
use App\Genre;
use App\Language;
use App\Section;
use App\Sequence;
use DOMDocument;
use DOMXpath;
use ErrorException;
use Exception;
use ImagickException;
use Litlife\Fb2\Fb2;
use Litlife\Fb2\Tag;
use Litlife\Fb2ToHtml\Fb2ToHtml;
use Litlife\Url\Url;

class AddFb2File
{
	public $sections = [];
	public $notes = [];
	public $book = null;
	private $binarySignatureArray = [];

	function __construct(&$book = null)
	{
		$this->fb2 = new Fb2;

		if (empty($book))
			$this->book = new Book;
		else
			$this->book = &$book;
	}

	public function setFile($path)
	{
		$this->fb2->loadFile($path);

		$this->addOrReplaceSectionIds();
		$this->getBinarySignatureArray();
	}

	private function addOrReplaceSectionIds()
	{
		$sections = $this->fb2->xpath->query("//*[local-name()='body']//*[local-name()='section']", $this->fb2->fictionBook()->getNode());

		$n = 1;

		foreach ($sections as $number => $section) {

			if ($section->hasAttribute('id')) {

				$aList = $this->fb2->xpath->query("//*[local-name()='a'][@" . $this->fb2->prefix . ":href='#" . $section->getAttribute('id') . "']");

				foreach ($aList as $a) {
					$a->setAttribute($this->fb2->prefix . ':href', '#section_' . $n);
				}
			}

			$section->setAttribute('id', 'section_' . $n);

			$n++;
		}

		$notes = $this->fb2->xpath->query("//*[local-name()='body'][@name='notes']//*[local-name()='section']", $this->fb2->fictionBook()->getNode());

		$n = 1;

		foreach ($notes as $number => $note) {

			if ($note->hasAttribute('id')) {

				$aList = $this->fb2->xpath->query("//*[local-name()='a'][@" . $this->fb2->prefix . ":href='#" . $note->getAttribute('id') . "']");

				foreach ($aList as $a) {
					$a->setAttribute($this->fb2->prefix . ':href', '#note_' . $n);
				}
			}

			$note->setAttribute('id', 'note_' . $n);

			$n++;
		}
	}

	public function getBinarySignatureArray()
	{
		foreach ($this->fb2->getBinariesArray() as $binary) {

			try {
				$this->binarySignatureArray[$binary->getId()][] = $binary->getImagick()->getImageSignature();

				$attachment = new Attachment();
				$attachment->openImage($binary->getImagick());

				$this->binarySignatureArray[$binary->getId()][] = $attachment->getImagick()->getImageSignature();
			} catch (ImagickException $exception) {

			}
		}
	}

	public function open($path)
	{
		$this->fb2->setFile($path);

		$this->addOrReplaceSectionIds();
		$this->getBinarySignatureArray();
	}

	public function setBook(&$book)
	{
		$this->book = &$book;
	}

	public function init()
	{
		$this->addImages();
		$this->description();
		$this->addSections();
		$this->addNotes();

		return true;
	}

	public function addImages()
	{
		$binaries = $this->fb2->getBinariesArray();

		foreach ($binaries as $number => $image) {

			if ($image->isValidImage()) {
				if (empty($image->getContentType()))
					$contentType = $image->getImagick()->getImageMimeType();
				else
					$contentType = $image->getContentType();

				$attachment = new Attachment;
				$attachment->storage = config('filesystems.default');
				$attachment->name = $image->getId();
				$attachment->content_type = $contentType;
				$attachment->size = $image->getImagick()->getImageLength();
				$attachment->type = 'image';
				$attachment->openImageNotThroughImagick($image->getContent(), 'blob');

				if (!$this->book->attachments()->whereSha256Hash($attachment->getSha256Hash())->first()) {
					$attachment->addParameter('w', $image->getImagick()->getImageWidth());
					$attachment->addParameter('h', $image->getImagick()->getImageHeight());
					$attachment->addParameter('fb_name', $image->getId());
					$this->book->attachments()->save($attachment);
				}
			}
		}

		$this->book->load('attachments');
	}

	public function description()
	{
		$description = $this->fb2->description();

		if (!empty($description)) {
			$titleInfo = $description->getFirstChild('title-info');
			$publishInfo = $description->getFirstChild('publish-info');

			if (!empty($titleInfo)) {

				if (!empty($titleInfo->getFirstChild('book-title'))) {
					$this->book->title = $titleInfo->getFirstChild('book-title')->getNodeValue();
				} else {
					$this->book->title = __('book.without_a_title');
				}

				if ($titleInfo->hasChild('lang')) {
					if ($lang = Language::where('code', 'ilike', $titleInfo->getFirstChildValue('lang'))->first()) {
						$this->book->ti_lb = $lang->code;
					}
				}

				if ($titleInfo->hasChild('src-lang')) {
					if ($lang = Language::where('code', 'ilike', $titleInfo->getFirstChildValue('src-lang'))->first()) {
						$this->book->ti_olb = $lang->code;
					}
				}

				$this->addAuthors();
				$this->addTranslators();
				$this->addGenres();
				$this->addSequences();
				$this->addCover();
				$this->addAnnotation();
			}

			if (!empty($publishInfo)) {
				$this->book->pi_pub = $publishInfo->getFirstChildValue('publisher');
				$this->book->pi_city = $publishInfo->getFirstChildValue('city');
				$this->book->pi_year = intval($publishInfo->getFirstChildValue('year'));
				$this->book->pi_isbn = $publishInfo->getFirstChildValue('isbn');
			}
		}

		$this->book->save();
		$this->book->refresh();
	}

	private function addAuthors()
	{
		$this->book->writers()->detach();

		$titleInfo = $this->fb2->description()->getFirstChild('title-info');

		if (!empty($titleInfo)) {
			foreach ($titleInfo->childs('author') as $order => $authorTag) {

				$name = $authorTag->getFirstChildValue('last-name') . ' ' .
					$authorTag->getFirstChildValue('first-name') . ' ' .
					$authorTag->getFirstChildValue('middle-name') . ' ' .
					$authorTag->getFirstChildValue('nickname');

				if (mb_strlen(trim($name)) > 2) {
					$author = Author::acceptedOrBelongsToUser($this->book->create_user)
						->searchByNameParts(
							$authorTag->getFirstChildValue('last-name'),
							$authorTag->getFirstChildValue('first-name'),
							$authorTag->getFirstChildValue('middle-name'),
							$authorTag->getFirstChildValue('nickname')
						)
						->notMerged()
						->first();

					if (empty($author)) {
						$author = new Author;
						$author->fill([
							'last_name' => $authorTag->getFirstChildValue('last-name'),
							'first_name' => $authorTag->getFirstChildValue('first-name'),
							'middle_name' => $authorTag->getFirstChildValue('middle-name'),
							'nickname' => $authorTag->getFirstChildValue('nickname'),
						]);
						$author->create_user()->associate($this->book->create_user);
						$author->save();
					}

					$this->book->writers()->syncWithoutDetaching([$author->id => ['order' => $order]]);
				}
			}
		}
	}

	private function addTranslators()
	{
		$this->book->translators()->detach();

		$titleInfo = $this->fb2->description()->getFirstChild('title-info');

		if (!empty($titleInfo)) {
			foreach ($titleInfo->childs('translator') as $order => $authorTag) {

				$name = $authorTag->getFirstChildValue('last-name') . ' ' .
					$authorTag->getFirstChildValue('first-name') . ' ' .
					$authorTag->getFirstChildValue('middle-name') . ' ' .
					$authorTag->getFirstChildValue('nickname');

				if (mb_strlen(trim($name)) > 2) {

					$author = Author::acceptedOrBelongsToUser($this->book->create_user)
						->searchByNameParts(
							$authorTag->getFirstChildValue('last-name'),
							$authorTag->getFirstChildValue('first-name'),
							$authorTag->getFirstChildValue('middle-name'),
							$authorTag->getFirstChildValue('nickname')
						)
						->notMerged()
						->first();

					if (empty($author)) {
						$author = new Author;
						$author->fill([
							'last_name' => $authorTag->getFirstChildValue('last-name'),
							'first_name' => $authorTag->getFirstChildValue('first-name'),
							'middle_name' => $authorTag->getFirstChildValue('middle-name'),
							'nickname' => $authorTag->getFirstChildValue('nickname'),
						]);
						$author->create_user()->associate($this->book->create_user);
						$author->save();
					}

					$this->book->translators()->syncWithoutDetaching([$author->id => ['order' => $order]]);
				}
			}
		}
	}

	/*
		private function section($fb2_section, $parent_section = null)
		{
			if ($fb2_section->getHtmlExceptTitleAndSection() or $fb2_section->isHaveImages() or $fb2_section->isHaveInnerSections()) {

				$nodeList = $this->fb2->xpath->query("./*[not(name()='title' or name()='section')]", $fb2_section->getNode());

				$fb2ToHtml = new Fb2ToHtml();
				$fb2ToHtml->setFb2Prefix($this->fb2->getPrefix());
				$fb2ToHtml->setClassPrefix(config('litlife.class_prefix'));
				$xhtml = $fb2ToHtml->toHtml($nodeList);

				$content = $this->handleContent($xhtml);

				$title = $fb2_section->getTitle();

				$section = new Section;
				$section->scoped(['book_id' => $this->book->id, 'type' => 'section']);
				$section->type = 'section';
				$section->title = empty($title) ? 'Без названия' : $title;
				$section->content = $content ?? '';
				$section->addParameter('section_id', appendPrefix(config('litlife.class_prefix'), $fb2_section->getFb2Id()));
				$this->book->sections()->save($section);

				if (!empty($parent_section))
					$section->appendToNode($parent_section)->save();

				foreach ($fb2_section->getSections() as $s) {
					$this->section($s, $section);
				}
			}
		}
		*/

	private function addGenres()
	{
		$this->book->genres()->detach();

		foreach ($this->fb2->description()->getFirstChild('title-info')->query('*[local-name()=\'genre\']') as $genreTag) {
			$genreName = $genreTag->getNodeValue();
			if ($genre = Genre::where('fb_code', 'ilike', $genreName)->orWhere('name', 'ilike', $genreName)->first()) {
				$this->book->genres()->syncWithoutDetaching([$genre->id]);
			}
		}
	}

	private function addSequences()
	{
		$this->book->sequences()->detach();

		$titleInfo = $this->fb2->description()->getFirstChild('title-info');

		if (!empty($titleInfo)) {
			foreach ($titleInfo->childs('sequence') as $order => $sequenceTag) {

				$name = $sequenceTag->getNode()->getAttribute('name');
				$number = $sequenceTag->getNode()->getAttribute('number');

				// если имя серии пустое, то не добавляем
				if (!empty($name)) {
					$sequence = Sequence::acceptedOrBelongsToUser($this->book->create_user)
						->where('name', 'ILIKE', $name)
						->notMerged()
						->first();

					if (empty($sequence)) {
						$sequence = new Sequence;
						$sequence->name = $name;
						$sequence->create_user()->associate($this->book->create_user);
						$sequence->save();
					}

					$this->book->sequences()->syncWithoutDetaching([
						$sequence->id => [
							'number' => empty($number) ? null : (int)$number,
							'order' => $order
						]]);
				}
			}
		}
	}

	public function addCover()
	{
		if (!empty($this->fb2->description())) {

			$image = $this->fb2->description()
				->query("*[local-name()='title-info']/*[local-name()='coverpage']/*[local-name()='image']")
				->first();

			if (!empty($image)) {
				$name = $image->getNode()
					->getAttribute('l:href');

				$name = ltrim($name, '#');

				if (!empty($name)) {

					$name = urldecode($name);

					if (array_key_exists((string)$name, $this->binarySignatureArray)) {
						$signature = $this->binarySignatureArray[(string)$name];

						$attachment = $this->book->attachments()
							->whereSha256Hash($signature)
							->first();

						if (!empty($attachment)) {
							$this->book->cover()->associate($attachment);
						}
					}
				}
			}
		}
	}

	public function addAnnotation()
	{
		$titleInfo = $this->fb2->description()->getFirstChild('title-info');

		if ($titleInfo) {

			$annotation = $titleInfo->getFirstChild('annotation');

			if ($annotation) {
				$nodeList = $this->fb2->xpath->query("./*", $annotation->getNode());

				$fb2ToHtml = new Fb2ToHtml();
				$fb2ToHtml->setFb2Prefix($this->fb2->getPrefix());
				$fb2ToHtml->setClassPrefix(config('litlife.class_prefix'));
				$xhtml = $fb2ToHtml->toHtml($nodeList);

				$content = $this->handleContent($xhtml);

				if (!$annotation->isHaveImages()) {
					if (empty(strip_tags($content))) {
						return false;
					}
				}

				$annotation = new Section;
				$annotation->inner_id = 0;
				$annotation->book_id = $this->book->id;
				$annotation->title = '';
				$annotation->type = 'annotation';
				$annotation->content = $content ?? '';
				//$annotation->addParameter('id', appendPrefix(config('litlife.class_prefix'), $fb2_section->getFb2Id()));
				$annotation->save();
			}
		}
	}

	public function handleContent($content)
	{
		$dom = new DOMDocument();

		try {
			$dom->loadHTML('<meta http-equiv="Content-Type" content="text/html; charset=utf-8">' . $content);
		} catch (ErrorException $exception) {
			$status = libxml_use_internal_errors();
			libxml_use_internal_errors(true);
			$dom->loadHTML('<meta http-equiv="Content-Type" content="text/html; charset=utf-8">' . $content);
			libxml_use_internal_errors($status);
		}

		$xpath = new DOMXpath($dom);
		$body = $dom->getElementsByTagName('body')->item(0);

		$nodeList = $xpath->query("//img[@src]", $body);

		if ($nodeList->length) {

			foreach ($nodeList as $node) {

				$url = $node->getAttribute("src");

				if (mb_substr($url, 0, 1) == '#')
					$url = mb_substr($url, 1);

				$url = Url::fromString($url);

				if (empty($url->getHost())) {

					/*
					$attachment = $this->book->attachments->first(function ($item, $key) use ($url) {
						if ($item->getParameter('fb_name') == urldecode($url))
							return true;
					});
					*/

					$url = urldecode($url);

					if (array_key_exists((string)$url, $this->binarySignatureArray)) {

						$signature = $this->binarySignatureArray[(string)$url];

						$attachment = $this->book->attachments()
							->whereSha256Hash($signature)
							->first();

						if (!empty($attachment)) {
							$node->setAttribute("src", $attachment->url);

							if ($attachment->getWidth())
								$node->setAttribute("width", $attachment->getWidth());

							if ($attachment->getHeight())
								$node->setAttribute("height", $attachment->getHeight());

							$prefix = appendPrefix(config('litlife.class_prefix'), 'attachment-');

							if ($node->hasAttribute("class")) {
								$class = $node->getAttribute("class");

								$class = preg_replace("/[[:space:]]+/iu", " ", $class);

								$class = preg_replace("/" . preg_quote($prefix) . "([0-9]+)/iu", "", $class);

								$class_ar = explode(' ', $class);
								$class_ar[] = $prefix . $attachment->id;
								$class = implode(' ', $class_ar);

								$node->setAttribute("class", $class);
							} else {
								$node->setAttribute("class", $prefix . $attachment->id);
							}
						}
					}
				}
			}
		}

		$nodeList = $xpath->query("//a[@href]", $body);

		if ($nodeList->length) {

			foreach ($nodeList as $node) {

				$url = Url::fromString($node->getAttribute("href"));

				if (empty($url->getHost())) {
					$fragment = $url->getFragment();

					if (!empty($fragment))
						$fragment = appendPrefix(config('litlife.class_prefix'), $fragment);

					$node->setAttribute("href", "#" . $fragment);
				}
			}
		}

		$content = '';

		if (isset($body->childNodes)) {
			foreach ($body->childNodes as $childNode) {
				$content .= $dom->saveHTML($childNode);
			}
		}

		return $content;
	}

	public function addSections()
	{
		$bodies = $this->fb2->getBodies();

		if (count($bodies) > 1) {
			foreach ($this->fb2->getBodies() as $body) {
				$this->section('section', $body);
			}
		} else {
			foreach ($this->fb2->getBodies() as $body) {
				foreach ($body->childs('section') as $fb2_section) {
					$this->section('section', $fb2_section);
				}
			}
		}

		$this->book->load('sections');
	}

	private function section($type, $fb2Section, $parent_section = null)
	{
		if (!in_array($type, ['section', 'note']))
			throw new Exception('type неверный');

		if (!$this->isAddSection($fb2Section))
			return null;

		$nodeList = $this->fb2->xpath->query("./*[not(name()='title' or name()='section')]", $fb2Section->getNode());

		$fb2ToHtml = new Fb2ToHtml();
		$fb2ToHtml->setFb2Prefix($this->fb2->getPrefix());
		$fb2ToHtml->setClassPrefix(config('litlife.class_prefix'));
		$xhtml = $fb2ToHtml->toHtml($nodeList);

		$content = $this->handleContent($xhtml);

		$title = $this->getSectionTitle($fb2Section);

		if (!$fb2Section->isHaveImages() and !$fb2Section->isHaveInnerSections()) {
			if (empty(strip_tags($content))) {
				return false;
			}
		}

		$section = new Section;
		$section->scoped(['book_id' => $this->book->id, 'type' => $type]);
		$section->type = $type;
		$section->title = empty($title) ? __('section.untitled') : $title;
		$section->content = $content ?? '';
		$section->addParameter('section_id', appendPrefix(config('litlife.class_prefix'), $fb2Section->getNode()->getAttribute('id')));
		$this->book->sections()->save($section);

		if ($type == 'section') {
			if (!empty($parent_section))
				$section->appendToNode($parent_section)->save();
		}

		foreach ($fb2Section->query("*[local-name()='section']") as $s) {
			$this->section($type, $s, $section);
		}
	}

	public function isAddSection(Tag $section)
	{
		if ($section->query("//*[local-name()='section']")->count() > 0)
			return true;

		if ($section->query("//*[local-name()='image']")->count() > 0)
			return true;

		foreach ($section->query("./*[not(name()='title' or name()='section')]") as $node) {
			$value = $node->getNodeValue();
			$value = preg_replace("/[[:space:]]+/iu", " ", $value);
			$value = trim($value);

			if ($value != '')
				return true;
		}

		return false;
	}

	public function getSectionTitle(Tag $section)
	{
		$titleNodesList = $this->fb2->xpath->query("./*[local-name()='title']", $section->getNode());

		$title = '';

		if ($titleNodesList->length) {

			foreach ($titleNodesList as $titleNode) {

				$paragraphNodesList = $this->fb2->xpath->query("./*[local-name()='p']", $titleNode);

				if ($paragraphNodesList->length > 0) {
					foreach ($paragraphNodesList as $paragraphNode) {
						$title .= $paragraphNode->nodeValue . ' ';
					}
				} else {
					$title .= $titleNode->nodeValue . ' ';
				}
			}
		}

		$title = trim($title);

		if ($title == '') {

			$childs = $this->fb2->xpath->query("./*", $section->getNode());

			if ($childs->length) {

				foreach ($childs as $child) {
					if (trim($child->nodeValue) != "") {
						$title = trim($child->nodeValue);

						if (mb_strlen($title) > 100) {
							$title = mb_substr($title, 0, 96) . ' ...';
						}

						break 1;
					}
				}
			}
		}

		$title = preg_replace("/[[:space:]]+/iu", " ", $title);

		return trim($title);
	}

	public function addNotes()
	{
		//$this->book->sections()->forceDelete();

		foreach ($this->fb2->getBodiesNotes() as $body) {
			foreach ($body->childs('section') as $fb2_note) {
				$this->section('note', $fb2_note);
			}
		}
	}
}

?>