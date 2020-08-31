<?php

namespace Litlife\Epub;

class Css extends File
{
	public $xpath;
	protected $epub;
	protected $css;

	function __construct(Epub $epub, $path = null)
	{
		parent::__construct($epub, $path);

		if (!empty($path)) {
			$this->css = $epub->zipFile->getEntryContents($path);
		} else {

		}
	}

	public function setPath($path)
	{
		$this->path = $path;
		$this->epub->files[$path] = $this;
	}

	public function loadCss($css)
	{
		$this->css = $css;
	}

	public function getContent()
	{
		return $this->css;
	}
}