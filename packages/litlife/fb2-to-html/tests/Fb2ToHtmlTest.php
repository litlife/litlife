<?php

namespace Litlife\Fb2ToHtml\Tests;

use DOMDocument;
use Litlife\Fb2ToHtml\Fb2ToHtml;
use PHPUnit\Framework\TestCase;

class Fb2ToHtmlTest extends TestCase
{
	public function testPoemTag()
	{
		$xml = <<<EOT
<poem>
<stanza>
<v>линия</v>
<v>линия</v>
<v>линия</v>
</stanza>
<text-author>Автор</text-author>
</poem>
EOT;

		$html = <<<EOT
<div class="u-poem">
<div class="u-stanza">
<p>линия</p>
<p>линия</p>
<p>линия</p>
</div>
<div class="u-text-author">Автор</div>
</div>
EOT;

		$this->assertEquals($html, $this->toHtml($xml));
	}

	public function toHtml($s, $class_prefix = null)
	{
		$dom = new DOMDocument();
		$dom->loadXML('<FictionBook xmlns="http://www.gribuser.ru/xml/fictionbook/2.0" xmlns:l = "http://www.w3.org/1999/xlink">' . $s . '</FictionBook>');

		$nodes = $dom->getElementsByTagName('FictionBook')->item(0)->childNodes;

		$class = new Fb2ToHtml();
		$class->setFb2Prefix('l');

		if (!empty($class_prefix))
			$class->setClassPrefix($class_prefix);

		$html = $class->toHtml($nodes);

		return $html;
	}

	public function testEpigraphTag()
	{
		$text = <<<EOT
<epigraph>
<p>текст</p>
<text-author>Автор</text-author>
</epigraph>
EOT;

		$fb2 = <<<EOT
<div class="u-epigraph">
<p>текст</p>
<div class="u-text-author">Автор</div>
</div>
EOT;

		$this->assertEquals($fb2, $this->toHtml($text));
	}

	public function testNewLines()
	{
		$text = <<<EOT
<strong>text
</strong>
EOT;

		$fb2 = <<<EOT
<b>text
</b>
EOT;

		$this->assertEquals($fb2, $this->toHtml($text));
	}

	public function testCiteTag()
	{
		$xml = <<<EOT
<cite> 
  <p>текст</p> 
  <text-author>От автора</text-author> 
</cite> 
EOT;

		$html = <<<EOT
<blockquote> 
  <p>текст</p> 
  <div class="u-text-author">От автора</div> 
</blockquote> 
EOT;

		$this->assertEquals($html, $this->toHtml($xml));
	}

	public function testEmphasisTag()
	{
		$xml = <<<EOT
<p>
  <emphasis>текст</emphasis>
</p>
EOT;

		$html = <<<EOT
<p>
  <i>текст</i>
</p>
EOT;

		$this->assertEquals($html, $this->toHtml($xml));
	}

	public function testStrongTag()
	{
		$xml = <<<EOT
<p>
  <strong>текст</strong>
</p>
EOT;

		$html = <<<EOT
<p>
  <b>текст</b>
</p>
EOT;

		$this->assertEquals($html, $this->toHtml($xml));
	}

	public function testStrikeThroungTag()
	{
		$xml = <<<EOT
<p>
  <strikethrough>текст</strikethrough>
</p>
EOT;

		$html = <<<EOT
<p>
  <s>текст</s>
</p>
EOT;

		$this->assertEquals($html, $this->toHtml($xml));
	}

	public function testStanzaTag()
	{
		$xml = <<<EOT
      <stanza>
        <v>текст</v>
      </stanza>
EOT;

		$html = <<<EOT
      <div class="u-stanza">
        <p>текст</p>
      </div>
EOT;

		$this->assertEquals($html, $this->toHtml($xml));
	}

	public function testSubtitleTag()
	{
		$xml = <<<EOT
      <cite>
        <subtitle>текст</subtitle>
        <p>текст</p>
      </cite>
EOT;

		$html = <<<EOT
      <blockquote>
        <div class="u-subtitle">текст</div>
        <p>текст</p>
      </blockquote>
EOT;

		$this->assertEquals($html, $this->toHtml($xml));
	}

	public function testTitleTag()
	{
		$xml = <<<EOT
      <section>
        <title>Заголовок</title>
        <p>текст</p>
      </section>
EOT;

		$html = <<<EOT
      <section>
        <div class="u-title">Заголовок</div>
        <p>текст</p>
      </section>
EOT;

		$this->assertEquals($html, $this->toHtml($xml));
	}

	public function testATag()
	{
		$xml = <<<EOT
        <p><a l:href="#note1&amp;note" type="note">Ссылка</a></p>
EOT;

		$html = <<<EOT
        <p><a class="u-note" href="#note1&amp;note">Ссылка</a></p>
EOT;

		$this->assertEquals($html, $this->toHtml($xml));
	}

	public function testImageTag()
	{
		$xml = <<<EOT
        <p><image l:href="#pic&amp;ture.jpg?test=test&amp;test2=test2"/></p>
EOT;

		$html = <<<EOT
        <p><img src="#pic&amp;ture.jpg?test=test&amp;test2=test2"/></p>
EOT;

		$this->assertEquals($html, $this->toHtml($xml));
	}

	public function testEmptyLineTag()
	{
		$xml = <<<EOT
        <p>текст</p>
        <empty-line/>
        <p>текст</p>
EOT;

		$html = <<<EOT
        <p>текст</p>
        <div class="u-empty-line"></div>
        <p>текст</p>
EOT;

		$this->assertEquals($html, $this->toHtml($xml));
	}

	public function testCodeTag()
	{
		$xml = <<<EOT
        <p>
        <code>код</code>
        </p>
EOT;

		$html = <<<EOT
        <p>
        <code>код</code>
        </p>
EOT;

		$this->assertEquals($html, $this->toHtml($xml));
	}

	public function testDateTag()
	{
		$xml = <<<EOT
<poem>
<stanza>
<v>текст</v>
</stanza>
<date value="2010-01-01">01.01.2010</date>
</poem>
EOT;

		$html = <<<EOT
<div class="u-poem">
<div class="u-stanza">
<p>текст</p>
</div>
<div class="date">01.01.2010</div>
</div>
EOT;

		$this->assertEquals($html, $this->toHtml($xml));
	}

	public function testAnnotationTag()
	{
		$xml = <<<EOT
<annotation>
<p>текст</p>
</annotation>
EOT;

		$html = <<<EOT
<div class="u-annotation">
<p>текст</p>
</div>
EOT;

		$this->assertEquals($html, $this->toHtml($xml));
	}

	public function testChangeClassPrefix()
	{
		$xml = <<<EOT
<annotation>
<p>текст</p>
</annotation>
EOT;

		$html = <<<EOT
<div class="prefix-annotation">
<p>текст</p>
</div>
EOT;

		$this->assertEquals($html, $this->toHtml($xml, 'prefix-'));
	}

	public function testNodeList()
	{
		$xml = <<<EOT
<strong>текст</strong>
<emphasis>текст</emphasis>
EOT;

		$html = <<<EOT
<b>текст</b>
<i>текст</i>
EOT;

		$this->assertEquals($html, $this->toHtml($xml));
	}

	public function testComments()
	{
		$xml = <<<EOT
<!-- комментарий -->
<strong>текст</strong>
EOT;

		$html = <<<EOT

<b>текст</b>
EOT;

		$this->assertEquals($html, $this->toHtml($xml));
	}

	public function testIdAttribute()
	{
		$xml = <<<EOT
      <section id="section12">
        <title>Заголовок</title>
        <p>текст</p>
      </section>
EOT;

		$html = <<<EOT
      <section id="section12">
        <div class="u-title">Заголовок</div>
        <p>текст</p>
      </section>
EOT;

		$this->assertEquals($html, $this->toHtml($xml));
	}

	public function testAHrefAttribute()
	{
		$xml = <<<EOT
        <a href="http://example.com" type="note">Ссылка</a>
EOT;

		$html = <<<EOT
        <a>Ссылка</a>
EOT;

		$this->assertEquals($html, $this->toHtml($xml));
	}

	public function testSubTag()
	{
		$xml = <<<EOT
<sub>текст</sub>
EOT;

		$html = <<<EOT
<sub>текст</sub>
EOT;

		$this->assertEquals($html, $this->toHtml($xml));
	}

	public function testSupTag()
	{
		$xml = <<<EOT
<sup>текст</sup>
EOT;

		$html = <<<EOT
<sup>текст</sup>
EOT;

		$this->assertEquals($html, $this->toHtml($xml));
	}

	public function testTableTag()
	{
		$xml = <<<EOT
<table>
<tr>
<th>1</th>
</tr>
<tr>
<td>1</td>
</tr>
</table>
EOT;

		$html = <<<EOT
<table>
<tr>
<th>1</th>
</tr>
<tr>
<td>1</td>
</tr>
</table>
EOT;

		$this->assertEquals($html, $this->toHtml($xml));
	}

	public function testStyleTag()
	{
		$xml = <<<EOT
<style>text</style>
<p>text</p>
EOT;

		$html = <<<EOT
<p>text</p>
<p>text</p>
EOT;

		$this->assertEquals($html, $this->toHtml($xml));
	}

	public function testSelfClosedTags()
	{
		$xml = <<<EOT
<p><strong></strong></p>
<p><strong></strong>текст</p>
<p><image l:href="#_1.jpg"/></p>
EOT;

		$html = <<<EOT
<p><b></b></p>
<p><b></b>текст</p>
<p><img src="#_1.jpg"/></p>
EOT;

		$this->assertEquals($html, $this->toHtml($xml));
	}

	public function testStrongToBTag()
	{
		$xml = <<<EOT
<p><strong></strong></p>
EOT;

		$html = <<<EOT
<p><b></b></p>
EOT;

		$this->assertEquals($html, $this->toHtml($xml));
	}

	public function testARemoteUrl()
	{
		$xml = <<<EOT
<p>текст <a l:href="https://example.com/test/?t.e:s&amp;t~t&amp;est">Исх. 3:2</a> текст</p>
EOT;

		$html = <<<EOT
<p>текст <a href="https://example.com/test/?t.e:s&amp;t~t&amp;est">Исх. 3:2</a> текст</p>
EOT;

		$this->assertEquals($html, $this->toHtml($xml));
	}

	public function testSpecialChars()
	{
		$xml = '<p>' . htmlentities('& > <') . '</p>';

		$this->assertEquals('<p>&amp; &gt; &lt;</p>', $this->toHtml($xml));
	}
}
