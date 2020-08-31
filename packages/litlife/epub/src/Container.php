<?php

namespace Litlife\Epub;

use DOMDocument;
use DOMXpath;

class Container extends File
{
	private $dom;
	private $xpath;

	public function __construct(Epub $epub, $path = null)
	{
		parent::__construct($epub, $path);

		if (!empty($path)) {
			$this->dom = new DOMDocument();
			$this->dom->loadXML(trim($epub->zipFile->getEntryContents($path)));
		} else {
			$this->dom = new DOMDocument("1.0", "utf-8");
			$this->dom->formatOutput = true;

			$container = $this->dom->createElement('container');
			$container->setAttribute('version', '1.0');
			$container->setAttribute('xmlns', 'urn:oasis:names:tc:opendocument:xmlns:container');
			$this->dom->appendChild($container);

			$rootfiles = $this->dom->createElement('rootfiles');
			$container->appendChild($rootfiles);
		}
	}

	public function setPath($path)
	{
		$this->path = $path;
		$this->epub->files[$path] = $this;
		$this->epub->container = $this;
	}

	public function xpath()
	{
		$this->xpath = new DOMXpath($this->dom());
		return $this->xpath;
	}

	public function dom()
	{
		return $this->dom;
	}

	public function container()
	{
		return $this->dom()->getElementsByTagName('container')->item(0);
	}

	public function appendRootFile($fullPath, $mediaType)
	{
		$file = $this->dom()->createElement('rootfile');
		$file->setAttribute('full-path', $fullPath);
		$file->setAttribute('media-type', $mediaType);
		return $this->rootfiles()->appendChild($file);
	}

	public function rootfiles()
	{
		return $this->dom()->getElementsByTagName('rootfiles')->item(0);
	}

	public function getContent()
	{
		return $this->dom()->saveXML();
	}
}