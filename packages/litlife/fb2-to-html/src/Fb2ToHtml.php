<?php

namespace Litlife\Fb2ToHtml;

use DOMDocument;
use DOMNode;
use DOMNodeList;
use DOMXpath;

class Fb2ToHtml
{
	private $fb2_prefix;
	private $class_prefix;
	private $tagTranslate;

	function __construct()
	{
		$this->dom = new DOMDocument("1.0", "UTF-8");
		$this->xpath = new DOMXpath($this->dom);

		$this->class_prefix = 'u-';
		$this->refreshTagTranslate();
	}

	public function refreshTagTranslate()
	{
		$this->tagTranslate = [
			[
				'from' => 'epigraph',
				'to' => 'div',
				'class' => $this->class_prefix . 'epigraph'
			],
			[
				'from' => 'cite',
				'to' => 'blockquote'
			],
			[
				'from' => 'emphasis',
				'to' => 'i'
			],
			[
				'from' => 'strong',
				'to' => 'b'
			],
			[
				'from' => 'strikethrough',
				'to' => 's'
			],
			[
				'from' => 'text-author',
				'to' => 'div',
				'class' => $this->class_prefix . 'text-author'
			],
			[
				'from' => 'stanza',
				'to' => 'div',
				'class' => $this->class_prefix . 'stanza'
			],
			[
				'from' => 'subtitle',
				'to' => 'div',
				'class' => $this->class_prefix . 'subtitle'
			],
			[
				'from' => 'poem',
				'to' => 'div',
				'class' => $this->class_prefix . 'poem'
			],
			[
				'from' => 'v',
				'to' => 'p',
			],
			[
				'from' => 'title',
				'to' => 'div',
				'class' => $this->class_prefix . 'title'
			],
			[
				'from' => 'a',
				'handler' => 'a'
			],
			[
				'from' => 'image',
				'to' => 'img',
				'handler' => 'img'
			],
			[
				'from' => 'empty-line',
				'to' => 'div',
				'class' => $this->class_prefix . 'empty-line'
			],
			[
				'from' => 'code',
				'to' => 'code'
			],
			[
				'from' => 'date',
				'to' => 'div',
				'class' => 'date'
			],
			[
				'from' => 'section',
				'to' => 'section'
			],
			[
				'from' => 'annotation',
				'to' => 'div',
				'class' => $this->class_prefix . 'annotation'
			],
			[
				'from' => 'sub',
				'handler' => 'sub'
			],
			[
				'from' => 'sup',
				'handler' => 'sup'
			],
			[
				'from' => 'style',
				'to' => 'p'
			],
		];
	}

	function setFb2Prefix($fb2_prefix)
	{
		$this->fb2_prefix = $fb2_prefix;
	}

	function setClassPrefix($prefix)
	{
		$this->class_prefix = $prefix;
		$this->refreshTagTranslate();
	}

	function toHtml($nodes)
	{
		if ($nodes instanceof DOMNodeList) {
			$html = '';
			foreach ($nodes as $node) {
				if (!empty($convertedNode = $this->convertNode($node))) {
					$html .= $this->dom->saveXML($convertedNode);
				}
			}
			return $html;
		} elseif ($nodes instanceof DOMNode) {
			if (!empty($convertedNode = $this->convertNode($nodes))) {
				return $this->dom->saveXML($this->convertNode($convertedNode));
			}
		}
	}

	function convertNode($node)
	{
		if ($node->nodeType == XML_TEXT_NODE) {
			$textNode = $this->dom->createTextNode($node->nodeValue);

			return $textNode;
		} elseif ($node->nodeType == XML_ELEMENT_NODE) {
			if ($rule = $this->getRule($node->nodeName)) {

				if (isset($rule['to'])) {

					if ($rule['to'] == '')
						return null;

					$htmlElement = $this->dom->createElement($rule['to']);

				} else
					$htmlElement = $this->dom->createElement($node->nodeName);

				if (isset($rule['class'])) {
					$classAttr = $this->dom->createAttribute('class');
					$classAttr->value = $rule['class'];
					$htmlElement->appendChild($classAttr);
				}

				if ((isset($rule['handler'])) and (method_exists($this, 'tag_' . $rule['handler']))) {
					$method = 'tag_' . $rule['handler'];
					$htmlElement = $this->$method($node, $htmlElement);
				}

			} else {
				$htmlElement = $this->dom->createElement($node->nodeName);
			}

			if ($node->hasAttribute("id")) {
				$nameAttr = $this->dom->createAttribute('id');
				$nameAttr->value = $node->getAttribute("id");
				$htmlElement->appendChild($nameAttr);
			}

			if ($node->childNodes->length > 0) {
				$html = '';

				foreach ($node->childNodes as $childNode) {
					//echo $childNode->nodeType.'<br />';
					if (!is_null($child = $this->convertNode($childNode))) {
						$htmlElement->appendChild($child);
					}
				}
			} else {
				if (!in_array($htmlElement->nodeName, ['area', 'base', 'br', 'col', 'command', 'embed', 'hr', 'img',
					'input', 'keygen', 'link', 'meta', 'param', 'source', 'track', 'wbr'])) {
					$htmlElement->appendChild($this->dom->createTextNode(""));
				}
			}

			return $htmlElement;
		}
	}

	/*
		public function createEmptyTextNodeInsideEmptyTag()
		{
			dd($this->dom->saveXML());

			$nodes = $this->xpath->query('//*');

			foreach ($nodes as $node) {
				if ($node->childNodes->length < 0) {
					$empty_text = $this->dom->createTextNode("");
					$node->appendChild($empty_text);
				}
			}
		}
		*/

	function getRule($tagName)
	{
		foreach ($this->getTagTranslate() as $rule) {
			if ($rule['from'] == $tagName) return $rule;
		}
	}

	public function getTagTranslate()
	{
		return $this->tagTranslate;
	}

	function tag_a($node, $htmlElement)
	{
		$hrefNS = $this->fb2_prefix . ":href";

		if (mb_substr($node->getAttribute($hrefNS), 0, 1) == '#') {

			$typeAttr = $this->dom->createAttribute('class');
			$typeAttr->value = $this->class_prefix . 'note';
			$htmlElement->appendChild($typeAttr);

			if ($node->hasAttribute($hrefNS)) {
				$href = trim($node->getAttribute($hrefNS));
				$htmlElement->setAttribute('href', $href);
			}
		} elseif ($node->hasAttribute($hrefNS)) {
			/*
			$aTag = $this->dom->createAttribute('href');
			$aTag->value = $node->getAttribute($hrefNS);
			$htmlElement->appendChild($aTag);
			*/
			$htmlElement->setAttribute('href', $node->getAttribute($hrefNS));
		}

		return $htmlElement;
	}

	function tag_img($node, $htmlElement)
	{
		$hrefNS = $this->fb2_prefix . ":href";

		if ($node->hasAttribute($hrefNS)) {

			$src = $node->getAttribute($hrefNS);

			$htmlElement->setAttribute('src', $src);

			return $htmlElement;
		}
	}
}
