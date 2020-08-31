<?php

namespace Litlife\Epub;

use Litlife\Url\Url;

class AddSectionsIds
{
	private $counter;

	function __construct($epub)
	{
		$this->epub = &$epub;
		$this->counter = 0;
	}

	public function init()
	{
		foreach ($this->epub->getSectionsList() as &$section) {

			$this->counter++;

			$body = $section->body();

			$section_id = 'section-' . $this->counter;

			if ($body->hasAttribute('id')) {
				$old_section_id = $body->getAttribute('id');

				$this->replaceHash($old_section_id, $section_id);
			}

			$body->setAttribute('id', $section_id);
		}

		foreach ($this->epub->getSectionsList() as $number => &$section) {

			$nodes = $section->xpath()->query("//*[local-name()='a'][@href]");

			// находим все ссылки в главе
			if ($nodes->length) {
				foreach ($nodes as $node) {

					$href = Url::fromString(urldecode($node->getAttribute('href')));

					if (trim($href->getFragment()) == '') {

						$absolutePath = $href->getPathRelativelyToAnotherUrl($section->getPath());

						if ($foundSection = $this->findSection($absolutePath)) {
							$body_id = $this->findSection($absolutePath)->body()->getAttribute('id');

							$node->setAttribute('href', $href->withFragment($body_id));
						}
					}
				}
			}
		}
	}

	public function replaceHash($oldHash, $newHash)
	{
		foreach ($this->epub->getSectionsList() as $number => &$section) {

			$nodes = $section->xpath()->query("//*[local-name()='a'][@href]");

			// находим все ссылки в главе
			if ($nodes->length) {
				foreach ($nodes as $node) {

					$href = urldecode($node->getAttribute('href'));

					if (trim(Url::fromString($href)->getFragment()) == $oldHash) {

						$href = (string)Url::fromString($href)
							->withFragment($newHash);

						$node->setAttribute('href', $href);
					}
				}
			}
		}
	}

	public function findSection($absolutePath)
	{
		foreach ($this->epub->getSectionsList() as $number => &$section) {

			if ($section->getPath() == $absolutePath)
				return $section;
		}
	}
}