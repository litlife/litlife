<?php

namespace App\Listeners;

use App\Anchor;
use App\Section;
use DOMDocument;
use DOMXpath;

class UpdateSectionAnchorsListener
{
	/**
	 * Create the event listener.
	 *
	 * @return void
	 */
	public function __construct()
	{
		//
	}

	/**
	 * Handle the event.
	 *
	 * @param object $event
	 * @return void
	 */
	public function handle($event)
	{
		$deletedRows = Anchor::where('book_id', $event->section->book_id)
			->where('section_id', $event->section->inner_id)
			->delete();

		$dom = new DOMDocument();
		$dom->loadHTML('<meta http-equiv="Content-Type" content="text/html; charset=utf-8">' . $event->section->content);

		$xpath = new DOMXpath($dom);

		$body = $dom->getElementsByTagName('body')->item(0);

		$nodeList = $xpath->query("//a", $body);

		if ($nodeList->length) {

			foreach ($nodeList as $node) {

				if ($node->hasAttribute('href')) {
					$href = $node->getAttribute("href");

					if (mb_substr($href, 0, 1) == '#') {

						$href = mb_substr($href, 1);

						$anchor = new Anchor;
						$anchor->book_id = $event->section->book_id;
						$anchor->section_id = $event->section->inner_id;
						$anchor->name = $href;

						$link_to_section = Section::where('book_id', $event->section->book_id)
							->idSearch($anchor->name)->first();

						if (!empty($link_to_section)) {
							$anchor->link_to_section = $link_to_section->inner_id;
						}

						$anchor->save();

						if ($node->hasAttribute("class")) {
							$class = $node->getAttribute("class");

							$class = preg_replace("/[[:space:]]+/iu", " ", $class);

							$class = preg_replace("/" . preg_quote($event->section->prefix) . "anchor\-([0-9]+)/iu", "", $class);

							$class_ar = explode(' ', $class);
							$class_ar[] = $event->section->prefix . 'anchor-' . $anchor->id;
							$class = implode(' ', $class_ar);

							$node->setAttribute("class", $class);
						} else {
							$node->setAttribute("class", $event->section->prefix . 'anchor-' . $anchor->id);
						}
					}
				}
			}
		}

		$content = '';

		if (isset($body->childNodes)) {
			$content = '';

			foreach ($body->childNodes as $childNode) {
				$content .= $dom->saveHTML($childNode);
			}
		}

		$event->section->content = $content;

		$event->section->save();
	}
}
