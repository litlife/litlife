<?php

namespace Litlife\Epub;

class AddExtensionIfNotExist
{
	function __construct(&$epub)
	{
		$this->epub = &$epub;
	}

	public function addExtension()
	{
		foreach ($this->epub->getImages() as $number => $image) {

			if (empty($image->getExtension())) {

				$image->rename($image->getFileName() . '.' . mb_strtolower($image->guessExtension()));
			}
		}
	}
}