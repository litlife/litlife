<?php

namespace Litlife\Epub;

use Litlife\Url\Url;

class UnifyTagIds
{
	private $current_id;

	function __construct($epub, $prefix)
	{
		$this->epub = &$epub;
		$this->id_name = $prefix . 'note-';
	}

	public function unify()
	{
		$idSectionArray = $this->getIdSectionsWithRepeatedIdsArray();

		$this->current_id = $this->getMaxId();

		foreach ($idSectionArray as $id => $sections) {
			$first_section = $sections[0];

			foreach ($sections as $c => $section) {
				if ($c > 0) {

					$this->current_id++;

					$id_name = $this->id_name . '' . $this->current_id;

					$nodes = $section->xpath()->query("//*[@id]");

					foreach ($nodes as $node) {

						if ($node->getAttribute('id') == $id) {
							$node->setAttribute('id', $id_name);
						}
					}

					foreach ($this->epub->getSectionsList() as $number => &$section2) {

						if ($first_section != $section2) {
							$nodes = $section2->xpath()->query("//*[local-name()='a'][@href]");

							// находим все ссылки в главе

							foreach ($nodes as $node) {

								$href = $node->getAttribute('href');

								if (Url::fromString($href)->getFragment() == $id) {
									$href = (string)Url::fromString($href)
										->withFragment($id_name);

									$node->setAttribute('href', $href);
								}
							}
						}
					}
				}
			}
		}

	}

	public function getIdSectionsWithRepeatedIdsArray()
	{
		$array = [];

		foreach ($this->epub->getSectionsList() as $number => &$section) {
			$nodes = $section->xpath()->query("//*[@id]");
			foreach ($nodes as $node) {
				$array[$node->getAttribute('id')][] = &$section;
			}
		}

		$array2 = [];

		foreach ($array as $id => $sections) {
			if (count($sections) > 1)
				$array2[$id] = $sections;
		}

		return $array;
	}

	public function getMaxId()
	{
		$array = array_keys($this->getIdSectionsWithRepeatedIdsArray());

		foreach ($array as $id) {
			if (mb_substr($id, 0, strlen($this->id_name)) == $this->id_name) {

				$id = mb_substr($id, strlen($this->id_name));

				if (is_numeric($id)) {
					$ids[] = $id;
				}
			}
		}

		return empty($ids) ? 0 : max($ids);
	}
}