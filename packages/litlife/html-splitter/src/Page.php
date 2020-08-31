<?php

namespace Litlife\HtmlSplitter;

use DOMDocument;
use DOMNode;
use DOMXpath;

class Page
{
	private $dom;
	private $body;
	private $xpath;
	private $characters_count;

	public function __construct()
	{
		$this->dom = new DOMDocument();
		$this->dom->loadHTML('<html><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"></head><body></body></html>');
		$this->body = $this->dom->getElementsByTagName('body')->item(0);
		$this->xpath = new DOMXpath($this->dom);
	}

	public function getDOM()
	{
		return $this->dom;
	}

	public function getBody()
	{
		return $this->body;
	}

	public function getXpath()
	{
		return $this->xpath;
	}

	public function appendHtml(string $html)
	{
		$dom = new DOMDocument();
		$dom->loadHTML('<meta http-equiv="Content-Type" content="text/html; charset=utf-8">' . $html);
		$body = $dom->getElementsByTagName('body')->item(0);

		if (isset($body->childNodes) and $body->childNodes->count() > 0) {
			foreach ($body->childNodes as $node) {
				$this->appendNode($node);
			}
		}
	}

	public function appendNode(DOMNode $node)
	{
		$imported_node = $this->dom->importNode($node, true);

		$this->body->appendChild($imported_node);

		$this->characters_count = $this->characters_count + $this->getTextCharactersCountWithoutSpaces($imported_node->nodeValue);
	}

	private function getTextCharactersCountWithoutSpaces($text)
	{
		$text = preg_replace("/[[:space:]]+/iu", "", $text);
		return mb_strlen($text);
	}

	public function getCharactersCount()
	{
		return $this->characters_count;
	}

	public function getHtml()
	{
		$html = '';
		foreach ($this->body->childNodes as $node) {
			$html .= $this->dom->saveXML($node);
		}
		return trim($html);
	}

	public function getImagesCount()
	{
		return $this->body->getElementsByTagName('img')->length;
	}
}
