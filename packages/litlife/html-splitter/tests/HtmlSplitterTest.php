<?php

namespace Litlife\HtmlSplitter\Tests;

use DOMDocument;
use DOMElement;
use DOMNode;
use DOMXPath;
use Faker\Factory;
use Litlife\HtmlSplitter\HtmlSplitter;
use Litlife\HtmlSplitter\Page;
use PHPUnit\Framework\TestCase;

class HtmlSplitterTest extends TestCase
{
	public function testSplit()
	{
		$text1 = '<p>' . $this->getTextEqualsLength(100) . '</p>';
		$text2 = '<p>' . $this->getTextEqualsLength(100) . '</p>';

		$this->assertEquals(107, mb_strlen($text1));
		$this->assertEquals(107, mb_strlen($text2));

		$pages = (new HtmlSplitter)
			->setMaxCharactersCount(100)
			->setHtml($text1 . $text2)
			->split();

		$this->assertEquals($pages->page(1)->getHtml(), $text1);
		$this->assertEquals($pages->page(2)->getHtml(), $text2);
		$this->assertEquals(2, $pages->count());
		$this->assertEquals(mb_strlen(strip_tags($text1 . $text2)), $pages->getAllPagesCharactersCount());
	}

	public function getTextEqualsLength($number)
	{
		$text = Factory::create()->sentence($number);

		$text = preg_replace("/[[:space:]]+/iu", "", $text);

		return mb_substr($text, 0, $number);
	}

	public function testSplitOnlyOnePage()
	{
		$text = '<p>' . $this->getTextEqualsLength(90) . '</p>';

		$pages = (new HtmlSplitter)
			->setMaxCharactersCount(100)
			->setHtml($text)
			->split();

		$this->assertEquals(1, $pages->count());
		$this->assertNull($pages->page(0));
		$this->assertEquals($pages->page(1)->getHtml(), $text);
		$this->assertNull($pages->page(2));
		$this->assertEquals(mb_strlen(strip_tags($text)), $pages->getAllPagesCharactersCount());
	}

	public function testSplitLastPageWithImage()
	{
		$text1 = '<p>' . $this->getTextEqualsLength(100) . '</p>';
		$text2 = '<p><img src="image.jpg" alt=""/></p>';

		$pages = (new HtmlSplitter)
			->setMaxCharactersCount(100)
			->setHtml($text1 . $text2)
			->split();

		$this->assertEquals(2, $pages->count());
		$this->assertEquals($pages->page(1)->getHtml(), $text1);
		$this->assertEquals($pages->page(2)->getHtml(), $text2);
		$this->assertEquals(mb_strlen(strip_tags($text1 . $text2)), $pages->getAllPagesCharactersCount());
	}

	public function testSplitLastPageWithIframe()
	{
		$text1 = '<p>' . $this->getTextEqualsLength(100) . '</p>';
		$text2 = '<iframe></iframe>';

		$pages = (new HtmlSplitter)
			->setMaxCharactersCount(100)
			->setHtml($text1 . $text2)
			->split();

		$this->assertEquals(2, $pages->count());
		$this->assertEquals($text1, $pages->page(1)->getHtml());
		$this->assertEquals('<iframe/>', $pages->page(2)->getHtml());
		$this->assertEquals(mb_strlen(strip_tags($text1 . $text2)), $pages->getAllPagesCharactersCount());
	}

	public function testSplitLastPageWithTable()
	{
		$text1 = '<p>' . $this->getTextEqualsLength(100) . '</p>';
		$text2 = '<table><tr><td>text</td></tr></table>';

		$pages = (new HtmlSplitter)
			->setMaxCharactersCount(100)
			->setHtml($text1 . $text2)
			->split();

		$this->assertEquals(2, $pages->count());
		$this->assertEquals($text1, $pages->page(1)->getHtml());
		$this->assertEquals('<table><tr><td>text</td></tr></table>', $pages->page(2)->getHtml());
		$this->assertEquals(mb_strlen(strip_tags($text1 . $text2)), $pages->getAllPagesCharactersCount());
	}

	public function testSplitFirstPageWithTable()
	{
		$text = '<table><tr><td>text</td></tr></table>';

		$pages = (new HtmlSplitter)
			->setMaxCharactersCount(100)
			->setHtml($text)
			->split();

		$this->assertEquals(1, $pages->count());
		$this->assertEquals($pages->page(1)->getHtml(), $text);
		$this->assertEquals(mb_strlen(strip_tags($text)), $pages->getAllPagesCharactersCount());
	}

	public function testSplitDontAppendIfLastPageEmpty()
	{
		$text1 = '<p>' . $this->getTextEqualsLength(100) . '</p>';
		$text2 = '<div></div><div></div><div></div><div></div><div></div><div></div>';

		$pages = (new HtmlSplitter)
			->setMaxCharactersCount(100)
			->setHtml($text1 . $text2)
			->split();

		$this->assertEquals(1, $pages->count());
		$this->assertEquals($pages->page(1)->getHtml(), $text1);
		$this->assertEquals(mb_strlen(strip_tags($text1 . $text2)), $pages->getAllPagesCharactersCount());
	}

	public function testSplitFirstPageEmptyText()
	{
		$text = '<p></p>';

		$pages = (new HtmlSplitter)
			->setMaxCharactersCount(100)
			->setHtml($text)
			->split();

		$this->assertEquals(1, $pages->count());
		$this->assertEquals('<p/>', $pages->page(1)->getHtml());
	}

	public function testPageAppendNode()
	{
		$dom = new DOMDocument();
		$dom->loadHTML('<meta http-equiv="Content-Type" content="text/html; charset=utf-8"><body><p>text</p></body>');

		$page = new Page();
		$page->appendNode($dom->getElementsByTagName('p')->item(0));
		$this->assertEquals('<p>text</p>', $page->getHtml());
	}

	public function testNewPageAutoCreateParentNode()
	{
		$page = new Page();
		$dom = $page->getDOM();

		$this->assertInstanceOf(DOMDocument::class, $dom);
		$this->assertInstanceOf(DOMNode::class, $page->getBody());
		$this->assertEquals('body', $page->getBody()->tagName);
	}

	public function testPageAppendHtml()
	{
		$page = new Page();
		$page->appendHtml('<p>text</p>');
		$this->assertEquals('<p>text</p>', $page->getHtml());
	}

	public function testPageAppendEmptyString()
	{
		$page = new Page();
		$page->appendHtml('');
		$this->assertEquals('', $page->getHtml());
	}

	public function testPageAppendString()
	{
		$page = new Page();
		$page->appendHtml('123');
		$this->assertEquals('<p>123</p>', $page->getHtml());
	}

	public function testPageCharactersCount()
	{
		$page = new Page();
		$page->appendHtml('123');
		$this->assertEquals(3, $page->getCharactersCount());

		$page->appendHtml('<p>456</p>');
		$this->assertEquals(6, $page->getCharactersCount());

		$page->appendHtml('<p></p>');
		$this->assertEquals(6, $page->getCharactersCount());

		$page->appendHtml('<b></b>');
		$this->assertEquals(6, $page->getCharactersCount());

		$page->appendHtml('<img src="/image.jpg" alt="" />');
		$this->assertEquals(6, $page->getCharactersCount());
	}

	public function testPageGetImagesCount()
	{
		$page = new Page();

		$this->assertEquals(0, $page->getImagesCount());

		$page->appendHtml('<p><img src="image.jpg" alt=""></p>');

		$this->assertEquals(1, $page->getImagesCount());

		$page->appendHtml('<p><img src="image.jpg" alt=""></p>');

		$this->assertEquals(2, $page->getImagesCount());
	}

	public function testSplitGapNotOverflow()
	{
		$text1 = '<p>' . $this->getTextEqualsLength(105) . '</p>';
		$text2 = '<p>' . $this->getTextEqualsLength(105) . '</p>';
		$text2 .= '<p>' . $this->getTextEqualsLength(10) . '</p>';

		$pages = (new HtmlSplitter)
			->setMaxCharactersCount(100)
			->setGapCharactersCount(20)
			->setHtml($text1 . $text2)
			->split();

		$this->assertEquals(2, $pages->count());
		$this->assertEquals($pages->page(1)->getHtml(), $text1);
		$this->assertEquals($pages->page(2)->getHtml(), $text2);
	}

	public function testSplitGapOverflow()
	{
		$text1 = '<p>' . $this->getTextEqualsLength(105) . '</p>';
		$text2 = '<p>' . $this->getTextEqualsLength(20) . '</p>';

		$pages = (new HtmlSplitter)
			->setMaxCharactersCount(100)
			->setGapCharactersCount(20)
			->setHtml($text1 . $text2)
			->split();

		$this->assertEquals(2, $pages->count());
		$this->assertEquals($pages->page(1)->getHtml(), $text1);
		$this->assertEquals($pages->page(2)->getHtml(), $text2);
	}

	public function testAllTextCharactersCount()
	{
		$length = rand(10, 1000);

		$text = '<p>' . $this->getTextEqualsLength($length) . '</p>';

		$splitter = (new HtmlSplitter)
			->setHtml($text);

		$this->assertEquals($length, $splitter->getCharactersCount());
	}

	public function testGetLastPage()
	{
		$text1 = '<p>' . $this->getTextEqualsLength(105) . '</p>';
		$text2 = '<p>' . $this->getTextEqualsLength(50) . '</p>';

		$pages = (new HtmlSplitter)
			->setMaxCharactersCount(100)
			->setHtml($text1 . $text2)
			->split();

		$this->assertEquals(2, $pages->count());
		$this->assertEquals($pages->last()->getHtml(), $text2);
		$this->assertEquals(2, $pages->getLatestPageNumber());
	}

	public function testGetAllPagesCharactersCount()
	{
		$text1 = '<p>' . $this->getTextEqualsLength(105) . '</p>';
		$text2 = '<p>' . $this->getTextEqualsLength(50) . '</p>';

		$pages = (new HtmlSplitter)
			->setMaxCharactersCount(100)
			->setHtml($text1 . $text2)
			->split();

		$this->assertEquals(155, $pages->getAllPagesCharactersCount());
	}

	public function testDeletePage()
	{
		$text1 = '<p>' . $this->getTextEqualsLength(105) . '</p>';
		$text2 = '<p>' . $this->getTextEqualsLength(50) . '</p>';

		$pages = (new HtmlSplitter)
			->setMaxCharactersCount(100)
			->setHtml($text1 . $text2)
			->split();

		$this->assertEquals(2, $pages->count());
		$this->assertEquals($pages->last()->getHtml(), $text2);

		$pages->delete(2);

		$this->assertEquals(1, $pages->count());
		$this->assertEquals($pages->last()->getHtml(), $text1);
	}

	public function testFirstPageNumber()
	{
		$text1 = '<p>' . $this->getTextEqualsLength(105) . '</p>';
		$text2 = '<p>' . $this->getTextEqualsLength(50) . '</p>';

		$rand = rand(0, 10);

		$pages = (new HtmlSplitter)
			->setMaxCharactersCount(100)
			->setFirstPageNumber($rand)
			->setHtml($text1 . $text2)
			->split();

		$this->assertEquals(2, $pages->count());
		$this->assertEquals($pages->page($rand)->getHtml(), $text1);
		$this->assertEquals($pages->page($rand + 1)->getHtml(), $text2);
	}

	public function testIterator()
	{
		$text1 = '<p>' . $this->getTextEqualsLength(105) . '</p>';
		$text2 = '<p>' . $this->getTextEqualsLength(50) . '</p>';

		$pages = (new HtmlSplitter)
			->setMaxCharactersCount(100)
			->setFirstPageNumber(1)
			->setHtml($text1 . $text2)
			->split();

		//$this->assertEquals(2, $pages->count());

		foreach ($pages as $number => $page) {
			if ($number == 1)
				$this->assertEquals($text1, $page->getHtml());

			if ($number == 2)
				$this->assertEquals($text2, $page->getHtml());
		}
	}

	public function testNewLinesBeetweenPages()
	{
		$text = "<p>текст текст текст</p>\n\n<p>текст текст текст текст</p>\n";

		$pages = (new HtmlSplitter)
			->setMaxCharactersCount(15)
			->setFirstPageNumber(1)
			->setHtml($text)
			->split();

		$this->assertEquals(2, $pages->count());

		$pages->rewind();

		$this->assertEquals('<p>текст текст текст</p>', $pages->current()->getHtml());

		$pages->next();

		$this->assertEquals('<p>текст текст текст текст</p>', $pages->current()->getHtml());
	}

	public function testCharactersCountDontCountSpaces()
	{
		$text = "<p>текст текст \n\n текст   \r\n   </p>\r\n<p>текст текст \n\n текст   \r\n   </p>";

		$splitter = (new HtmlSplitter)
			->setMaxCharactersCount(100)
			->setFirstPageNumber(1)
			->setHtml($text);

		$this->assertEquals(30, $splitter->getCharactersCount());

		$pages = $splitter->split();

		$this->assertEquals(30, $pages->current()->getCharactersCount());

		//$this->assertEquals($text, $pages->current()->getHtml());
	}

	public function testSetDOM()
	{
		$text = "<body><p>текст текст</p><p>текст текст</p></body>";

		$dom = new DOMDocument();
		$dom->loadXML($text);

		$splitter = (new HtmlSplitter)
			->setMaxCharactersCount(100)
			->setFirstPageNumber(1)
			->setDOM($dom);

		$this->assertInstanceOf(HtmlSplitter::class, $splitter);
		$this->assertInstanceOf(DOMXPath::class, $splitter->getXpath());
		$this->assertInstanceOf(DOMElement::class, $splitter->getBody());
		$this->assertEquals('body', $splitter->getBody()->nodeName);

		$pages = $splitter->split();

		$this->assertEquals(1, $pages->count());
		$this->assertEquals('<p>текст текст</p><p>текст текст</p>', $pages->page(1)->getHtml());
	}

	public function testEmptyText()
	{
		$text = "";

		$pages = (new HtmlSplitter)
			->setMaxCharactersCount(15)
			->setFirstPageNumber(1)
			->setHtml($text)
			->split();

		$this->assertEquals(0, $pages->getAllPagesCharactersCount());
	}
}
