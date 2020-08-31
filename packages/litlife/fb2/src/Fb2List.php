<?php

namespace Litlife\Fb2;

use DOMNodeList;
use Iterator;

class Fb2List implements Iterator
{
	private $nodeList;
	private $fb2;
	private $position = 0;

	public function __construct(&$fb2, DOMNodeList &$nodeList)
	{
		$this->fb2 = &$fb2;
		$this->nodeList = &$nodeList;
	}

	public function count()
	{
		return $this->nodeList->length;
	}

	public function first()
	{
		return $this->item(0);
	}

	public function item($index)
	{
		$node = $this->nodeList->item($index);

		if (empty($node))
			return null;
		else {
			if ($node->nodeName == 'section') {
				return new Section($this->fb2, $node);
			} else {
				return new Tag($this->fb2, $node);
			}
		}
	}

	public function rewind()
	{
		$this->position = 0;
	}

	public function current()
	{
		$item = $this->nodeList->item($this->position);

		if ($item->nodeName == 'section') {
			return new Section($this->fb2, $item);
		} else {
			return new Tag($this->fb2, $item);
		}

	}

	public function key()
	{
		return $this->position;
	}

	public function next()
	{
		++$this->position;
	}

	public function valid()
	{
		$item = $this->nodeList->item($this->position);
		return isset($item);
	}

}
