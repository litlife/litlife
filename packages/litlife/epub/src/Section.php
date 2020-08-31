<?php

namespace Litlife\Epub;

use DOMDocument;
use DOMElement;
use DOMImplementation;
use DOMNodeList;
use DOMXpath;
use Exception;

class Section extends File
{
	public $dom;
	public $xpath;
	protected $epub;
	protected $linear = null;
	protected $title_id = null;

	function __construct(Epub $epub, $path = null)
	{
		parent::__construct($epub, $path);

		if (!empty($path)) {
			$html = $epub->zipFile->getEntryContents($path);

			$html = str_replace('&nbsp;', '&#160;', $html);

			$this->dom = new DOMDocument();
			$this->loadXML($html);
		} else {
			// создаем новый документ
			$imp = new DOMImplementation;

			$dtd = $imp->createDocumentType('html', '-//W3C//DTD XHTML 1.1//EN',
				'http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd');

			$this->dom = $imp->createDocument(null, "html", $dtd);
			$this->dom->encoding = "utf-8";
			$this->dom->documentElement->setAttribute('xmlns', 'http://www.w3.org/1999/xhtml');

			$head = $this->dom->createElement('head');

			$meta = $this->dom->createElement('meta');
			$meta->setAttribute('http-equiv', 'Content-Type');
			$meta->setAttribute('content', 'text/html; charset=utf-8');
			$head->appendChild($meta);

			$this->html()->appendChild($head);
			$this->html()->appendChild($this->dom->createElement('body'));
		}
	}

	public function loadXml($html)
	{
		$html = trim($html);
		$html = str_replace('&nbsp;', '&#160;', $html);
		$this->dom->loadXML($html);
	}

	public function html()
	{
		return $this->dom->documentElement;
	}

	public function setBodyHtml($html)
	{
		$html = str_replace('&nbsp;', '&#160;', $html);

		if (empty($this->body())) {
			$body = $this->dom()->createElement('body');
			$this->dom()->documentElement->appendChild($body);
		} else {
			$attributes = $this->body()->attributes;
			$body = $this->dom()->createElement('body');
			$this->html()->replaceChild($body, $this->body());
		}

		if ($html == strip_tags($html))
			$html = '<p>' . $html . '</p>';
		/*
				$dom = new \DOMDocument;
				try {
					$dom->loadXML('<body>' . $html . '</body>');
				} catch (\Exception $exception) {
					$dom->loadHTML('<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"></head><body>' . $html . '</body>');
				}

				if (is_object(libxml_get_last_error())) {
					$dom->loadHTML('<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"></head><body>' . $html . '</body>');
				}
				*/

		$nodeList = $this->importXhtml($html);

		$this->clearBody();

		while ($nodeList->length) {
			$this->body()->appendChild($nodeList->item(0));
		}
		/*
				if ($dom->getElementsByTagName('body')->item(0)) {
					$this->html()->replaceChild($this->dom()->importNode($dom->getElementsByTagName('body')->item(0), true), $body);
				} else {
					foreach ($dom->childNodes as $node) {
						$body->appendChild($this->dom()->importNode($node, true));
					}
				}
				*/

		if (!empty($attributes)) {
			foreach ($attributes as $attribute) {
				$this->body()->setAttribute($attribute->nodeName, $attribute->value);
			}
		}
	}

	public function body()
	{
		return $this->dom()->getElementsByTagName('body')->item(0);
	}

	public function dom()
	{
		return $this->dom;
	}

	public function importXhtml($xhtml): DOMNodeList
	{
		$dom = new DOMDocument;
		try {
			$dom->loadXML('<body>' . $xhtml . '</body>');
		} catch (Exception $exception) {
			$dom->loadHTML('<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"></head><body>' . $xhtml . '</body>');
		}

		if (is_object(libxml_get_last_error())) {
			$dom->loadHTML('<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"></head><body>' . $xhtml . '</body>');
		}

		$nodeList = $this->dom()
			->importNode($dom->getElementsByTagName('body')->item(0), true)
			->childNodes;

		return $nodeList;
	}

	public function clearBody()
	{
		$childs = [];

		foreach ($this->body()->childNodes as $node) {
			$childs[] = $node;
		}

		if (count($childs)) {
			foreach ($childs as $node) {
				$this->body()->removeChild($node);
			}
		}
	}

	public function setPath($path)
	{
		$this->path = $path;
		$this->epub->files[$path] = $this;
	}

	public function title(string $s = null): DOMElement
	{
		$titleNode = $this->dom()->getElementsByTagName('title')->item(0);

		if (isset($s)) {
			$oldTitleNode = $titleNode;

			$titleNode = $this->dom()->createElement('title');
			$titleNode->appendChild($this->dom()->createTextNode($s));

			if (empty($oldTitleNode)) {

				$this->head()->appendChild($titleNode);
			} else {
				$titleNode = $this->dom()->createElement('title');
				$titleNode->appendChild($this->dom()->createTextNode($s));
				$this->head()->replaceChild($titleNode, $oldTitleNode);
			}
		}

		return $titleNode;
	}

	public function head()
	{
		$headNode = $this->dom()->getElementsByTagName('head')->item(0);

		if (empty($headNode)) {
			$headNode = $this->dom()->createElement('head');

			$this->dom()->documentElement->appendChild($headNode);
		}

		return $headNode;
	}

	public function getTitle()
	{
		if ($this->epub->ncx()) {
			$title = $this->epub->ncx()->findTitleByFullPath($this->getPath());

			$title = $this->titleHandler($title);

			if ($title != '') {
				return $title;
			}
		}

		// пытаемся извлечь заголовок из тега h1 в body
		$titleNode = $this->xpath()->query("//*[local-name()='body']//*[local-name()='h1'][@class='title']");

		if ($titleNode->length) {
			$titleNode = $titleNode->item(0);
			$title = $this->titleHandler($titleNode->nodeValue);

			if ($title != '') {

				if ($id = $titleNode->getAttribute('id'))
					$this->setTitleId($id);

				return $title;
			}
		}

		// пытаемся извлечь заголовок из первого найденного класса у которого класс начинается на title
		$classElements = $this->xpath()->query("//*[local-name()='body']//*[@class]");

		foreach ($classElements as $classElement) {

			if (preg_match("/^title(\_\-[0-9])*$/iu", $classElement->getAttribute("class"))) {
				$title = strip_tags($this->dom()->saveHTML($classElement));
				$title = $this->titleHandler($title);

				if ($title != '') {

					if ($id = $classElement->getAttribute('id'))
						$this->setTitleId($id);

					return $title;
				}
			}
		}

		// пытаемся извлечь заголовок из тега h1 в body
		$h1Elements = $this->xpath()->query("//*[local-name()='body']//*[local-name()='h1']");

		if ($h1Elements->length) {
			$h1Element = $h1Elements->item(0);
			$title = $this->titleHandler($h1Element->nodeValue);

			if ($title) {

				if (!empty($id = $h1Element->getAttribute('id')))
					$this->setTitleId($id);

				return $title;
			}
		}

		// пытаемся извлечь заголовок из тега title
		$title = $this->xpath()->query("//*[local-name()='title']");

		if ($title->length) {
			$title = $title->item(0)->nodeValue;
			$title = $this->titleHandler($title);
			if ($title != '') {
				return $title;
			}
		}

		$title = $this->dom()->documentElement->nodeValue;

		$title = $this->titleHandler($title);

		if (mb_strlen($title) > 40)
			$title = trim(mb_substr($title, 0, 30)) . '...';

		if ($title != '') {
			return $title;
		}

		// если совсем никак не извлечь title то используем название файла

		return $this->getBaseName();
	}

	public function titleHandler($string): string
	{
		$string = (string)$string;
		$string = html_entity_decode($string);

		mb_substitute_character(0x20);
		$string = mb_convert_encoding($string, "UTF-8", "auto");
		$string = mb_str_replace(chr(194) . chr(160), ' ', $string);

		$string = preg_replace("/[[:space:]]/iu", " ", $string);
		$string = preg_replace("/([[:space:]]{2,})/iu", "  ", $string);
		$string = trim($string);
		return $string;
	}

	public function xpath()
	{
		$this->xpath = new DOMXpath($this->dom());
		return $this->xpath;
	}

	public function getBodyContent()
	{
		$content = '';

		foreach ($this->body()->childNodes as $childNode) {
			$str = $this->dom()->saveXML($childNode);
			$str = str_replace("&#13;", ' ', $str);
			$content .= trim($str);
		}

		return $this->newLinesAndSpacesToOneSpace($content);
	}

	public function newLinesAndSpacesToOneSpace($content)
	{
		return trim(preg_replace("/[[:space:]]+/iu", " ", $content));
	}

	public function getParentNavPointSrc()
	{
		$navPoint = $this->epub->ncx()->findNavPointByBaseName($this->getBaseName());

		if (empty($navPoint))
			return null;

		$parentNavPoint = $navPoint->xpath("parent::*")[0]->xpath("parent::*")[0];

		if (!empty($parentNavPoint)) {
			$content = @$parentNavPoint->xpath("*[local-name()='content']")[0];

			if (!empty($content)) {
				return @basename($content->attributes()->src);
			}
		}
	}

	public function setBodyId($id)
	{
		$this->body()->setAttribute('id', $id);
	}

	public function getBodyId()
	{
		return $this->body()->getAttribute('id');
	}

	public function write()
	{
		$content = $this->dom->saveXml();

		$this->setContent($content);
	}

	public function setContent($content)
	{
		parent::setContent($content);
	}

	public function getContent($formatOutput = true)
	{
		$xml = $this->dom()->saveXml();

		if ($formatOutput) {
			$dom = new DOMDocument('1.0');
			$dom->preserveWhiteSpace = false;
			$dom->loadXML($xml);
			$dom->formatOutput = true;

			return $dom->saveXML();
		} else {
			return $xml;
		}
	}

	public function prependBodyXhtml($xhtml)
	{
		$firstSibling = $this->body()->firstChild;

		$importedNodeList = $this->importXhtml($xhtml);

		foreach ($importedNodeList as $node) {
			$childs[] = $node;
		}

		if (empty($firstSibling)) {
			foreach ($childs as $node) {
				$this->body()->appendChild($node);
			}
		} else {
			foreach ($childs as $node) {
				$firstSibling->parentNode->insertBefore($node, $firstSibling);
			}
		}

		return true;
	}

	public function getLinear()
	{
		return $this->linear;
	}

	public function setLinear($linear)
	{
		$this->linear = $linear;
	}

	public function getTitleId()
	{
		return $this->title_id;
	}

	public function setTitleId($title_id)
	{
		$this->title_id = $title_id;
	}
}