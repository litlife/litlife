<?php

namespace Litlife\HtmlSplitter;

use DOMDocument;
use DOMElement;
use DOMXpath;

class HtmlSplitter
{
	private $dom;
	private $body;
	private $gap_characters_count = 0;
	private $max_characters_count = 8000;
	private $pages = [];
	private $first_page_number = 1;
	private $xpath;
	private $characters_count;

	public function setHtml($html)
	{
		$this->dom = new DOMDocument();
		$this->dom->loadHTML('<meta http-equiv="Content-Type" content="text/html; charset=utf-8">' . $html);
		$this->body = $this->dom->getElementsByTagName('body')->item(0);
		$this->xpath = new DOMXpath($this->dom);
		$this->getCharactersCount(true);

		return $this;
	}

	public function getCharactersCount($fresh = false)
	{
		if ($fresh) {
			if (!empty($this->body)) {
				$text = $this->body->nodeValue;
				$text = preg_replace("/[[:space:]]+/iu", "", $text);
				$this->characters_count = mb_strlen($text);
			}
		}

		return $this->characters_count;
	}

	public function setDom($dom)
	{
		$this->dom = $dom;

		$this->body = $this->dom->getElementsByTagName('body')->item(0);
		$this->xpath = new DOMXpath($this->dom);
		$this->getCharactersCount(true);

		return $this;
	}

	public function getXpath()
	{
		return $this->xpath;
	}

	public function dom()
	{
		return $this->dom;
	}

	public function getBody(): DOMElement
	{
		return $this->body;
	}

	public function setMaxCharactersCount($number)
	{
		$this->max_characters_count = $number;

		return $this;
	}

	public function setGapCharactersCount($number)
	{
		$this->gap_characters_count = $number;

		return $this;
	}

	public function setFirstPageNumber($number)
	{
		$this->first_page_number = $number;

		return $this;
	}

	public function split()
	{
		$pages = new Pages($this->first_page_number);

		if (isset($this->body->childNodes)) {

			foreach ($this->body->childNodes as $node) {

				$pages->currentPage()->appendNode($node);

				if ($pages->currentPage()->getCharactersCount() >= $this->max_characters_count) {
					if ($pages->getAllPagesCharactersCount() + $this->gap_characters_count <= $this->getCharactersCount()) {
						$pages->divide();
					}
				}
			}
		}

		if ($pages->count() > 1 and $pages->last()->getCharactersCount() < 1 and $pages->last()->getXpath()->query('//img | //iframe | //table')->count() < 1) {
			$pages->delete($pages->getLatestPageNumber());
		}

		return $pages;
	}

	public function getPages()
	{
		return $this->pages;
	}

	public function getPagesCount()
	{
		return count($this->pages);
	}
}
