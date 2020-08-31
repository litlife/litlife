<?php

namespace Litlife\Fb2;

class Section extends Tag
{
	public function getTitle()
	{
		$nodeList = $this->getFb2()->xpath->query("./n:title", $this->getNode());

		$title = '';

		if ($nodeList->length) {

			foreach ($nodeList as $node) {
				$title .= $node->nodeValue . ' ';
			}
		}

		$title = trim($title);

		if ($title == '') {

			$childs = $this->getFb2()->xpath->query("./*", $this->getNode());

			if ($childs->length) {

				foreach ($childs as $child) {
					if (trim($child->nodeValue) != "") {
						$title = trim($child->nodeValue);

						if (mb_strlen($title) > 100) {
							$title = mb_substr($title, 0, 96) . ' ...';
						}

						break 1;
					}
				}
			}
		}

		$title = preg_replace("/[[:space:]]+/iu", " ", $title);

		return trim($title);
	}

	public function getNodeValue()
	{
		return $this->getNode()->nodeValue;
	}

	public function getHtmlExceptTitleAndSection()
	{
		$nodeList = $this->getFb2()->xpath->query("./*[not(name()='title' or name()='section')]", $this->getNode());

		$content = '';

		if ($nodeList->length) {

			foreach ($nodeList as $node) {
				$content .= trim($this->getFb2()->toHtml($node));
			}
		}

		$content = preg_replace("/[[:space:]]+/iu", " ", $content);

		return trim($content);
	}

	public function getSections()
	{
		$sections = [];

		foreach ($this->getFb2()->xpath->query("./n:section", $this->getNode()) as $section) {
			$sections[] = new Section($this->getFb2(), $section);
		}

		return $sections;
	}

	public function getFb2Id()
	{
		if ($this->getNode()->hasAttribute('id'))
			return $this->getNode()->getAttribute('id');
		else
			return null;
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

	public function isHaveInnerSections()
	{
		return (bool)$this->getSectionsCount();
	}

	public function getSectionsCount()
	{
		return $this->getFb2()->xpath->query("*[name()='section']", $this->getNode())->length;
	}
}