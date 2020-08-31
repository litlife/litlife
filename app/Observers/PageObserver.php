<?php

namespace App\Observers;

use App\Page;

class PageObserver
{
	/**
	 * Происходит когда запись добавляется или обновляется
	 *
	 * @param Page $page
	 * @return void
	 */

	public function creating(Page $page)
	{
		$page->book_id = $page->section->book_id;

		if (empty($this->html_tags_ids)) {
			$this->html_tags_ids = null;
		}
	}

	public function saving(Page $page)
	{
		if ($page->isDomSet()) {

			$this->findIds($page);

			$xhtml = $page->getBodyXHTML();

			$page->setAttribute('content', $xhtml);

			$character_count = transform($xhtml, function ($content) {

				$content = strip_tags($content);
				$content = preg_replace("/[[:space:]]+/iu", "", $content);
				$count = mb_strlen($content);
				return $count;
			});

			$page->setAttribute('character_count', $character_count);
		}
	}

	private function findIds(&$page)
	{
		$body = $page->dom()->getElementsByTagName('body')->item(0);

		$nodeList = $page->xpath()->query("//*[@id]", $body);

		if ($nodeList->length) {
			foreach ($nodeList as $node) {
				$page->addHtmlId((string)$node->getAttribute("id"));
			}
		}
	}
}