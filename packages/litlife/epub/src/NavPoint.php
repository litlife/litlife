<?php

namespace Litlife\Epub;

use DOMXpath;

class NavPoint
{
	public function __construct($epub, $path = null)
	{

	}

	public function xpath()
	{
		if (empty($this->xpath))
			$this->xpath = new DOMXpath($this->dom());

		return $this->xpath;
	}


}