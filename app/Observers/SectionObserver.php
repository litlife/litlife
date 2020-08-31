<?php

namespace App\Observers;

use App\Jobs\Book\UpdateBookPagesCount;
use App\Page;
use App\Section;
use Litlife\HtmlSplitter\HtmlSplitter;
use Spatie\Url\Url;

class SectionObserver
{
	/**
	 * Listen to the User created event.
	 *
	 * @param Section $section
	 * @return void
	 */
	public function creating(Section $section)
	{
		if (!isset($section->inner_id)) {
			$section->inner_id = (Section::any()->where('book_id', $section->book_id)->max('inner_id') + 1);
		}

		if (empty($section->parameters['section_id']))
			$section->addParameter('section_id', appendPrefix(config('litlife.class_prefix'), 'section-' . $section->inner_id));

		$this->setAnnotationExists($section);

		if (empty($section->title)) {
			$section->title = __('section.untitled');
		}
	}

	/**
	 * Отмечаем в записи книиг аннтация существует или нет
	 *
	 */
	private function setAnnotationExists(&$section)
	{
		if ($section->type == 'annotation' and !empty($section->book)) {
			$section->book->annotation_exists = true;
			$section->book->save();
		}
	}

	/**
	 * Происходит когда запись добавляется или обновляется
	 *
	 * @param Section $section
	 * @return void
	 */
	public function saving(Section $section)
	{
		if ($section->contentChanged) {
			$this->removeNotExistedAndFreshImageUrls($section);
			$this->splitOnPages($section);
		}
	}

	private function removeNotExistedAndFreshImageUrls(&$section)
	{
		$body = $section->dom()->getElementsByTagName('body')->item(0);

		$attachments = $section->book->attachments;

		// удаляем изображения, которые находятся не на сервере

		$nodeList = $section->xpath()->query("//img[@src]", $body);

		if ($nodeList->length) {

			foreach ($nodeList as $node) {

				$src = Url::fromString($node->getAttribute("src"));

				$founded_attachment = null;

				if (!empty($section->book->attachments)) {
					$founded_attachment = $attachments->first(function ($attachment) use ($src) {

						$attachment_url = Url::fromString($attachment->url);

						if (
							($src->getHost() == $attachment_url->getHost()) and
							($src->getDirname() == $attachment_url->getDirname()) and
							($src->getBasename() == $attachment_url->getBasename())
						) {
							return $attachment;
						}
					});
				}

				if (empty($founded_attachment)) {
					$node->parentNode->removeChild($node);
				}
			}
		}
		/*
				if ($nodeList->length) {

					foreach ($nodeList as $node) {

						if (preg_match("/attachment\-([0-9]+)/iu", $node->getAttribute("class"), $matches)) {
							$attachment_id = intval($matches[1]);

							$attachment = $attachments->where('id', $attachment_id)->first();

							if (!empty($attachment))
								$node->setAttribute("src", $attachment->url);
						}
					}
				}
				*/
	}

	private function splitOnPages(&$section)
	{
		if ($section->saveXML() != $section->getContent()) {
			$pages = (new HtmlSplitter)
				->setMaxCharactersCount(config('litlife.max_symbols_on_one_page'))
				->setDom($section->dom())
				->split();

			$section->pagesAfterSplitter = $pages;
			$section->pages_count = $pages->count();
			$section->character_count = $pages->getAllPagesCharactersCount();
		}
	}

	/**
	 * Listen to the Section created event.
	 *
	 * @param Section $section
	 * @return void
	 */
	public function saved(Section $section)
	{
		$this->savePages($section);

		if (!empty($section->book)) {
			if ($section->isChanged('character_count') or $section->isChanged('status')) {
				$section->book->refreshCharactersCount();
				$section->book->refreshPrivateChaptersCount();
				$section->book->refreshSectionsCount();
			}
		}

		$section->contentChanged = false;
	}

	private function savePages(&$section)
	{
		if (!empty($section->pagesAfterSplitter)) {

			unset($section->pages);

			$section->pages()->delete();

			if ($section->pagesAfterSplitter->count()) {
				foreach ($section->pagesAfterSplitter as $number => $page) {

					$page_model = new Page;
					$page_model->setDOM($page->getDOM());
					$page_model->character_count = $page->getCharactersCount();
					$page_model->page = $number;

					if ($number == 1) {
						$page_model->addHtmlId($section->getSectionId());
						$page_model->addHtmlId($section->getTitleId());
					}

					$section->pages()->save($page_model);
				}
			}

			UpdateBookPagesCount::dispatch($section->book()->any()->first());

			$section->pagesAfterSplitter = null;
		}
	}

	/**
	 * Listen to the Section deleted event.
	 *
	 * @param Section $section
	 * @return void
	 */
	public function deleted(Section $section)
	{
		$this->setAnnotationNotExists($section);

		if ($section->isForceDeleting()) {
			$section->pages()->delete();
		}

		if (!empty($section->book)) {
			UpdateBookPagesCount::dispatch($section->book);
			$section->book->refreshCharactersCount();
			$section->book->refreshPrivateChaptersCount();

			if ($section->isSection())
				$section->book->refreshSectionsCount();
			elseif ($section->isNote())
				$section->book->refreshNotesCount();
		}
	}

	/**
	 * Отмечаем в записи книиг аннтация существует или нет
	 *
	 */
	private function setAnnotationNotExists(&$section)
	{
		if (($section->type == 'annotation') and (!empty($section->book))) {
			$section->book->annotation_exists = false;
			$section->book->save();
		}
	}

	/**
	 * Listen to the Section restored event.
	 *
	 * @param Section $section
	 * @return void
	 */
	public function restored(Section $section)
	{
		$this->setAnnotationExists($section);

		if (!empty($section->book)) {
			UpdateBookPagesCount::dispatch($section->book);
			$section->book->refreshCharactersCount();
			$section->book->refreshPrivateChaptersCount();
			$section->book->refreshSectionsCount();

			if ($section->isSection())
				$section->book->refreshSectionsCount();
			elseif ($section->isNote())
				$section->book->refreshNotesCount();
		}
	}
}