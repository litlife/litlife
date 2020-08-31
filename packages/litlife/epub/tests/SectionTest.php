<?php

namespace Litlife\Epub\Tests;

use DOMDocument;
use DOMNode;
use Litlife\Epub\Epub;
use Litlife\Epub\Section;

class SectionTest extends TestCase
{
	public function testGetSectionsList()
	{
		$epub = new Epub();
		$epub->setFile(__DIR__ . '/books/test.epub');

		$list = $epub->getSectionsList();
		$this->assertTrue(is_array($list));
		$this->assertEquals(2, count($list));
		$this->assertInstanceOf(Section::class, $list['OEBPS/Text/Section0001.xhtml']);
	}

	public function testSection()
	{
		$epub = new Epub();
		$epub->setFile(__DIR__ . '/books/test.epub');

		$section = $epub->getSectionByFilePath('OEBPS/Text/Section0001.xhtml');

		$this->assertInstanceOf(DOMNode::class, $section->head());
		$this->assertEquals('', $section->title()->nodeValue);
		$this->assertEquals('<p>Porro hic libero <a href="../Text/Section0002.xhtml">note</a> dolorem. Dolor <a id="anchor1">note</a> quia impedit et corrupti. Laborum quos sit facere ut at illum. Nobis accusantium libero <a href="../Text/Section0002.xhtml#section_20">sit</a> eos. Sunt quia nulla quibusdam dolores. Mollitia dolorum quisquam voluptatum aperiam. Aut voluptatum accusantium alias voluptatem rerum quis illo et. Reiciendis ab minima aut suscipit. Mollitia velit eligendi quidem est. Facere rerum qui ut recusandae explicabo temporibus. Animi aut architecto eos rerum aut. Amet est explicabo minima nulla. Consequatur esse voluptatem vel voluptatem. Molestiae ad omnis magni amet. Aliquam voluptates odit dolorem praesentium nulla ullam. Totam consectetur cupiditate laborum sequi esse. Exercitationem velit dolores ut natus accusamus. Non nulla error voluptatum qui eum nam. Voluptate fuga facere odio autem maiores. <img alt="test" src="../Images/test.png"/> </p>', $section->getBodyContent());
	}

	public function testNewSection()
	{
		$epub = new Epub();

		$section = new Section($epub);
		$section->setPath('OEBPS/Text/Section1.xhtml');
		$section->setBodyHtml('<p>123</p>');

		$s = <<<EOT
<?xml version="1.0" encoding="utf-8"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
  </head>
  <body>
    <p>123</p>
  </body>
</html>

EOT;

		$this->assertEquals($s, $section->getContent());
		$this->assertEquals('<p>123</p>', $section->getBodyContent());
	}

	public function testSetBodyId()
	{
		$epub = new Epub();

		$section = new Section($epub);
		$section->setPath('OEBPS/Text/Section1.xhtml');
		$section->setBodyId('body_id');
		$section->setBodyHtml('<p>123</p>');
		$this->assertEquals('body_id', $section->body()->getAttribute('id'));
	}

	public function testGetBodyId()
	{
		$epub = new Epub();

		$section = new Section($epub);
		$section->setPath('OEBPS/Text/Section1.xhtml');
		$section->setBodyId('body_id');
		$section->setBodyHtml('<p>123</p>');
		$this->assertEquals('body_id', $section->getBodyId());
	}

	public function testNbspEntities()
	{
		$epub = $this->newEpub();

		$html = '<p>text &nbsp; &amp; <img alt="test" src="../Images/test.png"/>text &nbsp;</p>';

		$section = new Section($epub);
		$section->setPath('OEBPS/Text/Section1.xhtml');
		$section->setBodyId('body_id');
		$section->setBodyHtml($html);

		$this->assertEquals('<p>text &amp; <img alt="test" src="../Images/test.png"/>text </p>',
			$section->getBodyContent());
	}

	public function testCariage()
	{
		$epub = $this->newEpub();

		$section = new Section($epub);
		$section->setPath('OEBPS/Text/Section1.xhtml');
		$section->setBodyId('body_id');
		$section->setBodyHtml("test \r test \r test");

		$this->assertEquals('<p>test test test</p>',
			$section->getBodyContent());
	}

	public function testSvg()
	{
		$html = <<<EOT
<?xml version="1.0" encoding="utf-8"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <title>Cover</title>
</head>
<body>
  <div style="text-align: center; padding: 0pt; margin: 0pt;">
    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" height="100%" preserveAspectRatio="xMidYMid meet" version="1.1" viewBox="0 0 340 332" width="100%">
      <image width="340" height="332" xlink:href="../Images/изображение.png"/>
    </svg>
  </div>
</body>
</html>

EOT;

		$epub = $this->newEpub();

		$section = new Section($epub);
		$section->setPath('OEBPS/Text/Section1.xhtml');
		$section->loadXml($html);

		$this->assertEquals($html, $section->dom()->saveXML());
	}

	public function testXML()
	{
		$html = <<<EOT
<?xml version="1.0" encoding="utf-8"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <title>Cover</title>
</head>
<body>
  <div style="text-align: center; padding: 0pt; margin: 0pt;">
    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" height="100%" preserveAspectRatio="xMidYMid meet" version="1.1" viewBox="0 0 340 332" width="100%">
      <image width="340" height="332" xlink:href="../Images/изображение.png"/>
    </svg>
  </div>
</body>
</html>

EOT;

		$dom = new DOMDocument();
		$dom->loadXML($html);

		$dom2 = new DOMDocument();
		$dom2->loadXML($dom->saveXML());

		$this->assertEquals($html, $dom2->saveXML());
	}

	public function testTitle()
	{
		$html = <<<EOT
<?xml version="1.0" encoding="utf-8"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <title>Cover</title>
</head>
<body>
  <div style="text-align: center; padding: 0pt; margin: 0pt;">
    <svg xmlns="http://www.w3.org/2000/svg" height="100%" preserveAspectRatio="xMidYMid meet" version="1.1" viewBox="0 0 340 332" width="100%" xmlns:xlink="http://www.w3.org/1999/xlink">
      <image width="340" height="332" xlink:href="../Images/изображение.png"/>
    </svg>
  </div>
</body>
</html>
EOT;
		$epub = $this->newEpub();

		$section = new Section($epub);
		$section->setPath('OEBPS/Text/Section1.xhtml');
		$section->loadXml($html);

		$this->assertNotNull($section->dom()->getElementsByTagName('title')->item(0));
		$this->assertEquals('Cover', $section->dom()->getElementsByTagName('title')->item(0)->nodeValue);
	}

	public function testLoadXmlGetBodyContent()
	{
		$html = <<<EOT
<?xml version="1.0" encoding="utf-8"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <title>Cover</title>
</head>
<body>
  <p>текст</p>
</body>
</html>

EOT;

		$epub = $this->newEpub();

		$section = new Section($epub);
		$section->setPath('OEBPS/Text/Section1.xhtml');
		$section->loadXml($html);

		$epub->opf()->appendToManifest('Section1.xhtml', 'Text/Section1.xhtml', 'application/xhtml+xml');
		$epub->opf()->appendToSpine('Section1.xhtml');

		$this->assertEquals('<p>текст</p>', $section->getBodyContent());
		$this->assertInstanceOf(Section::class, $epub->getFileByPath('OEBPS/Text/Section1.xhtml'));

		$string = $epub->outputAsString();

		$epub = new Epub();
		$epub->setFile($string);

		$this->assertInstanceOf(Section::class, $epub->getFileByPath('OEBPS/Text/Section1.xhtml'));
		$this->assertEquals('<p>текст</p>', $epub->getFileByPath('OEBPS/Text/Section1.xhtml')->getBodyContent());
	}

	public function testSetBodyXmlGetBodyContent()
	{
		$html = '<p>текст</p>';

		$epub = $this->newEpub();

		$section = new Section($epub);
		$section->setPath('OEBPS/Text/Section1.xhtml');
		$section->setBodyHtml($html);

		$epub->opf()->appendToManifest('Section1.xhtml', 'Text/Section1.xhtml', 'application/xhtml+xml');
		$epub->opf()->appendToSpine('Section1.xhtml');

		$this->assertEquals($html, $section->getBodyContent());
		$this->assertInstanceOf(Section::class, $epub->getFileByPath('OEBPS/Text/Section1.xhtml'));

		$string = $epub->outputAsString();

		$epub = new Epub();
		$epub->setFile($string);

		$this->assertInstanceOf(Section::class, $epub->getFileByPath('OEBPS/Text/Section1.xhtml'));
		$this->assertEquals($html, $epub->getFileByPath('OEBPS/Text/Section1.xhtml')->getBodyContent());
	}

	public function testSetBodyHtmlTagsWithoutParent()
	{
		$epub = $this->newEpub();

		$section = new Section($epub);
		$section->setPath('OEBPS/Text/Section1.xhtml');
		$section->setBodyId('body_id');
		$section->setBodyHtml('<p>старый текст</p>');
		$section->setBodyHtml('<p><strong>текст</strong> <i>текст</i></p><p><strong>текст</strong> <i>текст</i></p>');

		$this->assertEquals('<p><strong>текст</strong> <i>текст</i></p><p><strong>текст</strong> <i>текст</i></p>', $section->getBodyContent());
		$this->assertEquals('body_id', $section->getBodyId());
	}

	public function testFixHtmlSelfClosedTags()
	{
		$epub = $this->newEpub();

		libxml_use_internal_errors(false);

		$section = new Section($epub);
		$section->setPath('OEBPS/Text/Section1.xhtml');
		$section->setBodyHtml('<p>текст <img src="/image.jpg"> текст</p>');

		$this->assertEquals('<p>текст <img src="/image.jpg"/> текст</p>',
			$section->getBodyContent());

		libxml_use_internal_errors(true);

		$section = new Section($epub);
		$section->setPath('OEBPS/Text/Section1.xhtml');
		$section->setBodyHtml('<p>текст <img src="/image.jpg"> текст</p>');

		$this->assertEquals('<p>текст <img src="/image.jpg"/> текст</p>',
			$section->getBodyContent());
	}

	public function testDoubleQuote()
	{
		$xhtml = '<p>&amp;&lt;&gt;&quot;\'</p>';

		$epub = $this->newEpub();

		$section = new Section($epub);
		$section->setPath('OEBPS/Text/Section1.xhtml');
		$section->setBodyHtml($xhtml);

		$this->assertEquals('<p>&amp;&lt;&gt;"\'</p>', $section->getBodyContent());
	}

	public function testFormatOutput()
	{
		$epub = $this->newEpub();

		$section = new Section($epub);
		$section->setPath('OEBPS/Text/Section1.xhtml');
		$section->setBodyHtml('<ul><li>текст</li><li>текст</li></ul>');

		$xhtmlFormatOutput = <<<EOT
    <ul>
      <li>текст</li>
      <li>текст</li>
    </ul>
EOT;

		$xhtmlNotFormatOutput = <<<EOT
<ul><li>текст</li><li>текст</li></ul>
EOT;

		$this->assertStringContainsString($xhtmlFormatOutput, $section->getContent(true));
		$this->assertStringNotContainsString($xhtmlNotFormatOutput, $section->getContent(true));

		$this->assertStringNotContainsString($xhtmlFormatOutput, $section->getContent(false));
		$this->assertStringContainsString($xhtmlNotFormatOutput, $section->getContent(false));
	}

	public function testTitleSetGet()
	{
		$epub = $this->newEpub();

		$s = 'тест & < > ? & ';

		$section = new Section($epub);
		$section->setPath('OEBPS/Text/Section1.xhtml');
		$section->title($s);

		$this->assertEquals($s, $section->title()->nodeValue);
	}

	public function testLoadXmlNewLinesBeforeXML()
	{
		$epub = $this->newEpub();

		$section = new Section($epub);
		$section->setPath('OEBPS/Text/Section1.xhtml');
		$section->setBodyHtml('<ul><li>текст</li><li>текст</li></ul>');

		$content = "\r\n\r\n\r\n\r\n\r\n\r\n\r\n" . $section->getContent();

		$section = new Section($epub);
		$section->loadXml($content);
		$content = $section->getBodyContent();

		$this->assertEquals('<ul> <li>текст</li> <li>текст</li> </ul>', $content);
	}

	public function testImportXhtml()
	{
		$epub = $this->newEpub();

		$section = new Section($epub);
		$section->setPath('OEBPS/Text/Section1.xhtml');

		$nodes = $section->importXhtml('<p>текст <b>текст</b></p><p>текст2 <b>текст2</b></p>');

		$this->assertEquals('<p>текст <b>текст</b></p>', $section->dom()->saveXML($nodes->item(0)));
		$this->assertEquals('<p>текст2 <b>текст2</b></p>', $section->dom()->saveXML($nodes->item(1)));
	}

	public function testClearBody()
	{
		$epub = $this->newEpub();

		$html = <<<EOT
<?xml version="1.0" encoding="utf-8"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<body>
  <p>123</p>
  <p>456</p>
</body>
</html>
EOT;
		$section = new Section($epub);
		$section->setPath('OEBPS/Text/Section1.xhtml');
		$section->loadXml($html);

		$this->assertEquals("<p>123</p><p>456</p>", $section->getBodyContent());

		$section->clearBody();

		$this->assertEquals("", $section->getBodyContent());
	}

	public function testPrependBodyXhtml()
	{
		$epub = $this->newEpub();

		$section = new Section($epub);
		$section->setPath('OEBPS/Text/Section1.xhtml');

		$section->prependBodyXhtml('<p>текст</p>');

		$this->assertEquals('<p>текст</p>', $section->getBodyContent());

		$section->prependBodyXhtml('<p>текст2</p>');

		$this->assertEquals('<p>текст2</p><p>текст</p>', $section->getBodyContent());

		$section->prependBodyXhtml('<p>текст4</p><p>текст3</p>');

		$this->assertEquals('<p>текст4</p><p>текст3</p><p>текст2</p><p>текст</p>', $section->getBodyContent());
	}

	public function testGetTitleV1()
	{
		$epub = $this->newEpub();

		$section = new Section($epub);
		$section->setPath('OEBPS/Text/Section1.xhtml');
		$section->setBodyHtml('<h1 class="title">заголовок</h1><p>текст</p>');

		$this->assertEquals('заголовок', $section->getTitle());
		$this->assertEquals('<h1 class="title">заголовок</h1><p>текст</p>', $section->getBodyContent());
	}

	public function testGetTitleV2()
	{
		$epub = $this->newEpub();

		$section = new Section($epub);
		$section->setPath('OEBPS/Text/Section1.xhtml');
		$section->setBodyHtml('<p class="title">заголовок</p><p>текст</p>');

		$this->assertEquals('заголовок', $section->getTitle());
		$this->assertEquals('<p class="title">заголовок</p><p>текст</p>', $section->getBodyContent());
	}

	public function testGetTitleV3()
	{
		$epub = $this->newEpub();

		$section = new Section($epub);
		$section->setPath('OEBPS/Text/Section1.xhtml');
		$section->setBodyHtml('<h1>заголовок</h1><p>текст</p>');

		$this->assertEquals('заголовок', $section->getTitle());
		$this->assertEquals('<h1>заголовок</h1><p>текст</p>', $section->getBodyContent());
	}

	public function testGetTitleV4()
	{
		$epub = $this->newEpub();

		$xhtml = '<p>текст текст текст текст текст текст текст текст текст текст текст текст текст текст текст текст текст </p>';

		$section = new Section($epub);
		$section->setPath('OEBPS/Text/Section1.xhtml');
		$section->setBodyHtml($xhtml);

		$this->assertEquals('текст текст текст текст текст...',
			$section->getTitle());

		$this->assertEquals($xhtml,
			$section->getBodyContent());
	}

	public function testGetTitleV5()
	{
		$epub = $this->newEpub();

		$section = new Section($epub);
		$section->setPath('OEBPS/Text/Section1.xhtml');

		$this->assertEquals('Section1.xhtml',
			$section->getTitle());

		$this->assertEquals('',
			$section->getBodyContent());
	}

	public function testTitleHandler()
	{
		$epub = $this->newEpub();

		$section = new Section($epub);

		$this->assertEquals('Текст  текст', $section->titleHandler('Текст       текст'));
		$this->assertEquals('Текст & текст', $section->titleHandler('Текст &amp; текст'));
		$this->assertEquals('Текст  текст  текст', $section->titleHandler("Текст \r\n\r\n\r\n текст \n\n текст"));
		$this->assertEquals('Текст', $section->titleHandler("    Текст        "));
		$this->assertEquals('текст', $section->titleHandler("текст"));

		$title = 'текст   текст'; // asc 194
		$this->assertStringContainsString(chr(194), $title);
		$title = $section->titleHandler($title);
		$this->assertStringNotContainsString(chr(194), $title);
	}

	public function testIsLinear()
	{
		$epub = new Epub();
		$epub->setFile(__DIR__ . '/books/test_with_linear_no.epub');

		$array = $epub->getSectionsList();

		$this->assertEquals(null, $epub->getFileByPath('OEBPS/Text/Section0001.xhtml')->getLinear());
		$this->assertEquals('no', $epub->getFileByPath('OEBPS/Text/Section0002.xhtml')->getLinear());
	}

	public function testGetTitleId()
	{
		$epub = new Epub();
		$epub->setFile(__DIR__ . '/books/test_header_anchor.epub');

		$array = $epub->getSectionsList();

		$this->assertEquals('Глава 1', $epub->getFileByPath('OEBPS/Text/Section0001.xhtml')->getTitle());
		$this->assertEquals('header1', $epub->getFileByPath('OEBPS/Text/Section0001.xhtml')->getTitleId());
		$this->assertEquals('<h1 id="header1">Глава 1</h1><p>текст первой главы <a href="../Text/Section0002.xhtml#header2">сноска</a></p>',
			$epub->getFileByPath('OEBPS/Text/Section0001.xhtml')->getBodyContent());

		$this->assertEquals('Глава 2', $epub->getFileByPath('OEBPS/Text/Section0002.xhtml')->getTitle());
		$this->assertEquals('header2', $epub->getFileByPath('OEBPS/Text/Section0002.xhtml')->getTitleId());
		$this->assertEquals('<h1 id="header2">Глава 2</h1><p>текст второй главы <a href="../Text/Section0001.xhtml#header1">сноска</a></p>',
			$epub->getFileByPath('OEBPS/Text/Section0002.xhtml')->getBodyContent());

		$epub->getFileByPath('OEBPS/Text/Section0001.xhtml')->setTitleId('test');

		$this->assertEquals('test', $epub->getFileByPath('OEBPS/Text/Section0001.xhtml')->getTitleId());
	}
}
