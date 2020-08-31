<?php

namespace Tests\Feature\Book\Section;

use App\Section;
use Tests\TestCase;

class SectionContentPurifyTest extends TestCase
{
	public function testBasic()
	{
		$section = new Section();

		$content = <<<EOT
<p style="text-align:left;">текст <strong>strong</strong> <em>em</em> <s>s</s> <u>u</u> <sub>sub</sub> <sup>sup</sup></p>
EOT;
		$this->assertEquals($content, $section->purify($content));

		$content = <<<EOT
<p><span style="text-align:left;"></span> <b>b</b> <i>i</i> <br /> <u>u</u> </p>
EOT;
		$this->assertEquals($content, $section->purify($content));
	}

	public function testBlockquote()
	{
		$section = new Section;

		$content = <<<EOT
<blockquote>текст</blockquote>
EOT;
		$this->assertEquals($content, $section->purify($content));
	}

	public function testHr()
	{
		$section = new Section;

		$content = <<<EOT
<p>текст</p>
<hr />
<p>текст</p>
EOT;
		$this->assertEquals($content, $section->purify($content));
	}

	public function testA()
	{
		$section = new Section;

		$content = <<<EOT
<p><a name="test" id="test" href="/books/123">текст</a></p>
EOT;
		$this->assertEquals('<p><a id="u-test" href="/books/123">текст</a></p>', $section->purify($content));
	}

	public function testImg()
	{
		$section = new Section;

		$content = <<<EOT
<p><img width="100" height="100" src="/image.jpeg" alt="текст" style="text-align:left;" /></p>
EOT;
		$this->assertEquals($content, $section->purify($content));

		$section = new Section;

		$content = <<<EOT
<p><img width="100" height="100" src="/image.jpeg" alt="текст" class="u-image-align-left" /></p>
EOT;
		$this->assertEquals($content, $section->purify($content));

		$content = <<<EOT
<p><img width="100" height="100" src="/image.jpeg" alt="текст" class="u-image-align-right" /></p>
EOT;
		$this->assertEquals($content, $section->purify($content));

		$content = <<<EOT
<p class="u-image-align-center"><img width="100" height="100" src="/image.jpeg" alt="текст" /></p>
EOT;
		$this->assertEquals($content, $section->purify($content));

		$content = <<<EOT
<div class="u-image-align-center"><img width="100" height="100" src="/image.jpeg" alt="текст" /></div>
EOT;
		$this->assertEquals($content, $section->purify($content));
	}

	public function testTable()
	{
		$section = new Section;

		$content = <<<EOT
<table border="1" cellpadding="1" cellspacing="1" style="text-align:left;">
    <caption>текст</caption>
    <thead><tr><th style="text-align:left;">текст</th></tr></thead>
    <tbody><tr><td style="text-align:left;">текст</td></tr></tbody>
</table>
EOT;
		$this->assertEquals($content, $section->purify($content));
	}

	public function testList()
	{
		$section = new Section;

		$content = <<<EOT
<ol>
    <li>текст</li>
</ol>
<ul>
    <li>текст</li>
</ul>
EOT;
		$this->assertEquals($content, $section->purify($content));
	}

	public function testClasses()
	{
		$section = new Section;

		$content = <<<EOT
<div class="epigraph">текст</div>
<div class="text-author">текст</div>
<div class="stanza">текст</div>
<div class="subtitle">текст</div>
<div class="poem">текст</div>
<div class="title">текст</div>
<div class="empty-line">текст</div>
<div class="annotation">текст</div>
<div class="v">текст</div>
<div class="date">текст</div>
EOT;

		$this->assertEquals($content, $section->purify($content));
	}

	public function testClassesWithPrefix()
	{
		$section = new Section;

		$content = <<<EOT
<div class="u-epigraph">текст</div>
<div class="u-text-author">текст</div>
<div class="u-stanza">текст</div>
<div class="u-subtitle">текст</div>
<div class="u-poem">текст</div>
<div class="u-title">текст</div>
<div class="u-empty-line">текст</div>
<div class="u-annotation">текст</div>
<div class="u-v">текст</div>
<div class="u-date">текст</div>
EOT;

		$this->assertEquals($content, $section->purify($content));
	}

	public function testCssProperties()
	{
		$section = new Section;

		$content = <<<EOT
<p style="text-align:left;height:100px;width:100px;color:#FFF;background-color:#FFF;">текст</p>
EOT;
		$this->assertEquals($content, $section->purify($content));


		$section = new Section;
		$content = <<<EOT
<table style="border-spacing:7px 11px;border-collapse:separate;"><tr><td>123</td></tr></table>
EOT;
		$this->assertEquals($content, $section->purify($content));
	}

	public function testH()
	{
		$section = new Section;

		$content = <<<EOT
<h1>текст</h1>
<h2>текст</h2>
<h3>текст</h3>
<h4>текст</h4>
<h5>текст</h5>
<h6>текст</h6>
EOT;
		$this->assertEquals($content, $section->purify($content));

	}

	public function testD()
	{
		$section = new Section;

		$content = <<<EOT
<dl>
    <dt>test</dt>
    <dd>test</dd>
</dl>
EOT;
		$this->assertEquals($content, $section->purify($content));
	}

	public function testCol()
	{
		$section = new Section;

		$content = <<<EOT
<table>
    <col width="150" valign="top" />
    <tr>
        <td>текст</td>
    </tr>
</table>
EOT;
		$this->assertEquals($content, $section->purify($content));
	}

	public function testColgroup()
	{
		$section = new Section;

		$content = <<<EOT
<table>
    <colgroup span="9" align="center" width="50">
        <col span="5" />
        <col span="4" />
    </colgroup>
    <tr>
        <th colspan="2">текст</th>
    </tr><tr>
        <td colspan="2">текст</td>
    </tr>
</table>
EOT;
		$this->assertEquals($content, $section->purify($content));
	}

	public function testIframe()
	{
		$section = new Section;

		$content = <<<EOT
<iframe></iframe>
EOT;
		$this->assertEquals('', $section->purify($content));
	}

	public function testJavascript()
	{
		$section = new Section;

		$content = <<<EOT
<p>текст</p><script type="text/javascript">alert('123');</script><p>текст</p>
EOT;
		$this->assertEquals("<p>текст</p>\n\n<p>текст</p>", $section->purify($content));
	}

	public function testAway()
	{
		$section = new Section;

		$content = <<<EOT
<p><a href="http://example.com/папка/файл?ключ=значение#якорь" target="_blank">текст</a></p>
EOT;
		$this->assertEquals('<p><a href="/away?url=http%3A%2F%2Fexample.com%2F%25D0%25BF%25D0%25B0%25D0%25BF%25D0%25BA%25D0%25B0%2F%25D1%2584%25D0%25B0%25D0%25B9%25D0%25BB%3F%25D0%25BA%25D0%25BB%25D1%258E%25D1%2587%3D%25D0%25B7%25D0%25BD%25D0%25B0%25D1%2587%25D0%25B5%25D0%25BD%25D0%25B8%25D0%25B5%23%25D1%258F%25D0%25BA%25D0%25BE%25D1%2580%25D1%258C">текст</a></p>',
			$section->purify($content));
	}

	public function testRemoveSpansWithoutAttributes()
	{
		$section = new Section;

		$content = <<<EOT
<p><span style="color: #000;"><span><span>текст</span></span></span></p>
EOT;

		$this->assertEquals('<p><span style="color:#000;">текст</span></p>', $section->purify($content));

		$content = <<<EOT
<p><span style="color: #000;"><span><span style="text-align: left">текст</span></span></span></p>
EOT;

		$this->assertEquals('<p><span style="color:#000;"><span style="text-align:left;">текст</span></span></p>', $section->purify($content));
	}

	public function testDontTrimSpacesInsideTag()
	{
		$section = new Section;

		$content = <<<EOT
<p>   текст</p>
EOT;

		$this->assertEquals('<p>   текст</p>', $section->purify($content));
	}

	public function testNbspToNonBreakableSpace()
	{
		$section = new Section;

		$content = <<<EOT
<p>&nbsp;&nbsp; text&nbsp;text&nbsp;&nbsp;&nbsp;</p>
EOT;

		$this->assertEquals('<p>   text text   </p>', $section->purify($content));
	}
}
