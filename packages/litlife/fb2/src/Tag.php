<?php

namespace Litlife\Fb2;

use DOMElement;

class Tag
{
	protected $scheme = [];
	private $node;
	private $fb2;
	private $nodes = [];
	private $xpath;

	function __construct(Fb2 $fb2, $node = null)
	{
		$this->fb2 = $fb2;

		if (is_string($node)) {
			$this->node = $fb2->dom()->createElementNS($this->fb2->getNameSpace(), $node);
		} else {
			$this->node = $node;
		}

		$this->load();
	}

	public function load()
	{
		$this->nodes = [];

		foreach ($this->scheme as $name => $rule) {
			foreach ($this->fb2->xpath()->query('*[local-name()=\'' . $name . '\']', $this->node) as $node) {
				$this->nodes[$name][] = new Tag($this->fb2, $node);
			}
		}
	}

	public function getNodes()
	{
		return $this->nodes;
	}

	public function setAttribute($name, $value)
	{
		$this->getNode()->setAttributeNS($this->fb2->getNameSpace(), $name, $value);
	}

	public function getNode(): DOMElement
	{
		return $this->node;
	}

	public function getXML(): string
	{
		return $this->fb2->dom()->saveXML($this->getNode(), LIBXML_NOEMPTYTAG);
	}

	public function appendChild(Tag $tag)
	{
		return $this->getNode()->appendChild($tag->getNode());
	}

	public function create()
	{
		if (isset(func_get_args()[0]))
			$name = func_get_args()[0];

		if (isset(func_get_args()[1]))
			$value = func_get_args()[1];

		$name = str_replace('_', '-', $name);

		$tag = new Tag($this->fb2, $name);

		if (!empty($value)) {

			if (is_array($value))
				$value = pos($value);

			$tag->setValue($value);
		}

		$this->getNode()->appendChild($tag->getNode());

		return $tag;
	}

	public function setValue($value)
	{
		foreach ($this->getNode()->childNodes as $node) {
			$this->getNode()->removeChild($node);
		}

		$text = $this->fb2->dom()->createTextNode($value);

		$this->getNode()->appendChild($text);
	}

	public function delete()
	{
		$this->getNode()->parentNode->removeChild($this->getNode());
	}

	public function getParent()
	{
		return $this->getNode()->parentNode;
	}

	public function hasChild($name): bool
	{
		return (boolean)$this->query("*[local-name()='" . $name . "']")->first();
	}

	public function query($query)
	{
		$nodeList = $this->fb2->xpath->query($query, $this->getNode());

		return new Fb2List($this->fb2, $nodeList);
	}

	public function getFirstChildValue($name)
	{
		$child = $this->getFirstChild($name);

		if (!empty($child))
			return $child->getNodeValue();
	}

	public function getFirstChild($name)
	{
		foreach ($this->childs() as $child) {
			if ($child->getNodeName() == $name)
				return $child;
		}
	}

	public function childs($name = null)
	{
		if (empty($name)) {
			$nodeList = $this->fb2->xpath->query('child::*', $this->getNode());
		} else {
			$nodeList = $this->fb2->xpath->query("child::*[local-name()='" . $name . "']", $this->getNode());
		}
		return new Fb2List($this->fb2, $nodeList);
	}

	public function getNodeName()
	{
		return $this->getNode()->nodeName;
	}

	public function getNodeValue()
	{
		return $this->getNode()->nodeValue;
	}

	public function isHaveImages()
	{
		$childs = $this->getFb2()->xpath->query("*[not(name()='title' or name()='section')]", $this->getNode());

		if ($childs->length) {

			foreach ($childs as $child) {
				//dump('child '.$child->nodeName);
				if ($child->nodeName == 'image') {
					return true;
				} else {
					$childDescendants = $this->getFb2()->xpath->query("*", $child);
					foreach ($childDescendants as $descendant) {
						//dump('descendant '.$descendant->nodeName);
						if ($descendant->nodeName == 'image')
							return true;
					}
				}
			}
		}

		return false;
	}

	public function getFb2(): Fb2
	{
		return $this->fb2;
	}

	public function isHaveInnerSections()
	{
		return (bool)$this->getSectionsCount();
	}

	public function getSectionsCount()
	{
		return $this->getFb2()->xpath->query("*[name()='section']", $this->getNode())->length;
	}

	/*
	private $parent;
	protected $fb2;
	private $node;

	public function __call($name, $arguments)
	{

		if (method_exists($this, $name)) {
			$this->$name($arguments);
		} else {
			if ($tag = $this->getChildTagWithName($name)) {
				return $tag;
			} else {
				return $this->create($name, $arguments);
			}
		}
	}

	function __construct(&$fb2, $name, $parent = null, $value = null)
	{
		$this->fb2 = &$fb2;
		$this->parent = $parent;

		if (is_string($name)) {
			$this->node = $this->fb2->dom->createElementNS($this->fb2->getNameSpace(), $name, $value);

			if (!empty($this->parent))
				$this->parent->appendChild($this->node);
		} elseif (is_object($name)) {
			$this->node = $name;
		}

		return $this;
	}



	public function getNode()
	{
		return $this->node;
	}









	public function getXml()
	{
		return $this->fb2->dom->saveXML($this->node);
	}




	public function getChildTagWithName($name)
	{
		$name = str_replace('_', '-', $name);

		if ($this->childs()->count()) {
			foreach ($this->childs() as $tag) {
				if ($tag->getNodeName() == $name)
					return $tag;
				else
					return false;
			}
		} else {
			return false;
		}
	}

	public function hasChild($name)
	{
		$name = str_replace('_', '-', $name);

		if ($this->childs()->count()) {
			foreach ($this->childs() as $tag) {
				if ($tag->getNodeName() == $name)
					return true;
				else
					return false;
			}
		} else {
			return false;
		}
	}




	*/
}
