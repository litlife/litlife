<?php

namespace Litlife\Epub;

class UnifyImagesNames
{
	function __construct($epub)
	{
		$this->epub = &$epub;

		$this->prefix = 'image_';

		$this->current_id = $this->getMaxId();
	}

	public function getMaxId()
	{
		foreach ($this->epub->getImages() as $number => &$image) {
			if (mb_substr(mb_strtolower($image->getFileName()), 0, strlen($this->prefix)) == $this->prefix) {

				$id = mb_substr(mb_strtolower($image->getFileName()), strlen($this->prefix));

				if (is_numeric($id)) {
					$ids[] = $id;
				}
			}
		}

		return empty($ids) ? 0 : max($ids);
	}

	public function createFileName()
	{
		return $this->prefix . $this->current_id;
	}

	public function unify()
	{
		$imagesNames = [];
		$imagesForRename = [];

		foreach ($this->epub->getImages() as $number => $image) {

			if (in_array(mb_strtolower($image->getBaseName()), $imagesNames)) {
				$imagesForRename[] = $image;
			} else {
				$imagesNames[] = mb_strtolower($image->getBaseName());
			}
		}

		foreach ($imagesForRename as $number => $image) {
			$this->current_id++;
			$image->rename($this->prefix . $this->current_id . '.' . $image->getExtension());
		}

		return $imagesForRename;
	}

}