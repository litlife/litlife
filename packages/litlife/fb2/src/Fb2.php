<?php

namespace Litlife\Fb2;

use DOMDocument;
use DOMXpath;
use Exception;

class Fb2
{
	public $dom;
	public $xpath;
	public $prefix = 'l';
	public $fictionBook;
	public $bodies = [];
	public $bodiesNotes = [];
	public $bodiesComments = [];
	public $binaries = [];
	private $description;
	private $namespace = 'http://www.gribuser.ru/xml/fictionbook/2.0';
	private $encoding = 'utf-8';
	private $scheme;

	public function __construct()
	{
		$this->dom = new DOMDocument('1.0', $this->encoding);

		$this->fictionBook = $this->createFictionBook();

		$this->dom->appendChild($this->fictionBook()->getNode());

		$this->xpath = new DOMXpath($this->dom);
		$this->xpath->registerNamespace('p', $this->namespace);
	}

	public function createFictionBook()
	{
		$fictionBook = new FictionBook($this, "FictionBook");
		$fictionBook->getNode()->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:' . $this->getPrefix(), "http://www.w3.org/1999/xlink");
		return $fictionBook;
	}

	public function getPrefix()
	{
		return $this->prefix;
	}

	public function fictionBook()
	{
		return $this->fictionBook;
	}

	public function setFile($path)
	{
		$this->loadFile($path);
	}

	public function loadFile($path)
	{
		if (is_string($path) and is_file($path)) {
			$this->loadXML(file_get_contents($path));
		} elseif (is_resource($path)) {
			$this->loadXML(stream_get_contents($path));
		} else {
			throw new Exception('File or resource invalid');
		}
	}

	public function loadXML($xml)
	{
		$xml = $this->fixHtmlEntities($xml);

		$this->dom = new DOMDocument();
		$this->dom->loadXML($xml);

		$this->xpath = new DOMXpath($this->dom);

		if (empty($this->dom->documentElement))
			throw new Exception('Root element not found');

		$this->fictionBook = new FictionBook($this, $this->dom->documentElement);

		$this->namespace = $this->dom->documentElement->lookupnamespaceURI(NULL);
		$this->xpath->registerNamespace('p', $this->namespace);

		$prefix = $this->parseNamespacePrefix();

		if ($prefix) {
			$this->prefix = $prefix;
		}

		if (empty($this->prefix)) {
			throw new Exception('Prefix empty');
		}

		$this->loadDescription();
		$this->loadBodies();
		$this->loadBodiesNotes();
		$this->loadBodiesComments();
		$this->loadBinaries();
	}

	public function fixHtmlEntities($xml)
	{
		$xml = str_replace('&nbsp;', ' ', $xml);

		return $xml;
	}

	public function parseNamespacePrefix()
	{
		return $this->dom()->documentElement->lookupPrefix('http://www.w3.org/1999/xlink');
	}

	public function dom()
	{
		return $this->dom;
	}

	public function loadDescription()
	{
		$description = $this->xpath->query("*[local-name()='description']", $this->fictionBook()->getNode())->item(0);

		if (empty($description))
			return null;

		$this->description = new Description($this, $description);

		return $this->description();
	}

	public function description()
	{
		if (empty($this->description)) {
			$this->description = $this->fictionBook()
				->create('description');
		}

		return $this->description;
	}

	public function loadBodies()
	{
		$this->bodies = [];

		foreach ($this->xpath->query("*[local-name()='body'][not(@name='notes' or @name='comments')]", $this->fictionBook()->getNode()) as $body) {
			$this->bodies[] = new Body($this, $body);
		}

		return $this->getBodies();
	}

	public function getBodies()
	{
		return $this->bodies;
	}

	public function loadBodiesNotes()
	{
		$this->bodiesNotes = [];

		$bodies = $this->xpath->query("*[local-name()='body'][@name='notes']", $this->fictionBook()->getNode());

		foreach ($bodies as $body) {
			$this->bodiesNotes[] = new Body($this, $body);
		}

		return $this->getBodiesNotes();
	}

	public function getBodiesNotes()
	{
		return $this->bodiesNotes;
	}

	public function loadBodiesComments()
	{
		$this->bodiesComments = [];

		$bodies = $this->xpath->query("*[local-name()='body'][@name='comments']", $this->fictionBook()->getNode());

		foreach ($bodies as $body) {
			$this->bodiesComments[] = new Body($this, $body);
		}

		return $this->getBodiesComments();
	}

	public function getBodiesComments()
	{
		return $this->bodiesComments;
	}

	public function loadBinaries()
	{
		$nodes = $this->xpath->query("*[local-name()='binary']", $this->fictionBook()->getNode());

		foreach ($nodes as $node) {
			$id = $node->getAttribute('id');
			$content_type = $node->getAttribute('content-type');

			$binary = new Binary($this, $id, $content_type);
			$binary->setContentAsBase64($node->nodeValue);
		}

		return $this->getBinariesArray();
	}

	public function getBinariesArray(): array
	{
		return $this->binaries;
	}

	public function getScheme()
	{
		$this->scheme = new Scheme;
		$this->scheme->loadSceme();

		return $this->scheme;
	}

	public function xpath()
	{
		return $this->xpath;
	}

	public function setOrGetValue($searchStr, $parent, $value = null)
	{
		if (isset($value)) {

			preg_match('/^(?:p\:?)(.*)$/iu', $searchStr, $matches);
			$node = $this->dom->createElementNS($this->getNameSpace(), $matches[1], $value);
			$parent->appendChild($node);
		}

		return $this->searchGetValue($searchStr, $parent);
	}

	public function getNameSpace()
	{
		return $this->namespace;
	}

	///

	public function searchGetValue($searchStr, $parentNode = null)
	{
		if (isset($parentNode))
			$nodes = $this->xpath->query($searchStr, $parentNode);
		else
			$nodes = $this->xpath->query($searchStr);

		if ($nodes->length) {
			return $nodes->item(0)->nodeValue;
		}
		return "";
	}

	public function query($query, $parent = null)
	{
		return $this->xpath->query($query, $parent ?? null);
	}

	///

	public function title($str = null)
	{
		if (isset($str))
			return $this->description()->title_info()->book_title($str);
		else
			return $this->description()->title_info()->book_title();
	}

	public function getXml()
	{
		return $this->dom->saveXml();
	}

	public function createElement($name, $value = null)
	{
		$tag = new Tag($this, $name);
		$tag->setValue($value);
		return $tag;
	}

	public function getBinaryByName($name)
	{
		return $this->binaries[$name];
	}

	public function isValid()
	{
		if (empty($this->getValidationErrors()))
			return true;
		else
			return false;
	}

	public function getValidationErrors()
	{
		libxml_clear_errors();
		$status = libxml_use_internal_errors();
		$dom = new DOMDocument('1.0', 'utf-8');
		$dom->loadXML($this->getContent());
		libxml_use_internal_errors(true);
		$dom->schemaValidate(__DIR__ . '/../xsd/FictionBook2.2.xsd');

		$errors = libxml_get_errors();
		foreach ($errors as $error) {
			$error->message = str_replace('{http://www.gribuser.ru/xml/fictionbook/2.0}', '', $error->message);
		}

		libxml_use_internal_errors($status);
		return $errors;
	}

	public function getContent($encoding = 'utf-8', $formatOutput = true)
	{
		$fb2 = clone $this;

		$fictionBook = $fb2->createFictionBook();

		$fictionBook->getNode()->appendChild($fb2->description()->getNode());

		foreach ($fb2->getBodies() as $body) {
			$fictionBook->getNode()->appendChild($body->getNode());
		}

		foreach ($fb2->getBodiesNotes() as $body) {
			$fictionBook->getNode()->appendChild($body->getNode());
		}

		foreach ($fb2->getBinariesArray() as $binary) {
			$node = $fb2->dom()->createElementNS($fb2->getNameSpace(), 'binary');
			$node->setAttribute('id', $binary->getId());
			$node->setAttribute('content-type', $binary->getContentType());

			$content = $fb2->dom()->createTextNode($binary->getContentAsBase64());
			$node->appendChild($content);

			$fictionBook->getNode()->appendChild($node);
		}

		$fb2->dom()->removeChild($fb2->dom()->documentElement);
		$fb2->dom()->appendChild($fictionBook->getNode());

		$fb2->dom()->encoding = $encoding;

		$xml = $this->dom()->saveXml(null, LIBXML_NOEMPTYTAG);

		if ($formatOutput == true) {
			$dom = new DOMDocument('1.0', $encoding);
			$dom->preserveWhiteSpace = false;
			$dom->loadXML($xml);
			$dom->formatOutput = true;

			return trim($dom->saveXML(null, LIBXML_NOEMPTYTAG));
		} else {
			return $xml;
		}
	}
}
