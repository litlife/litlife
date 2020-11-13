<?php

namespace Tests\Feature\Book\Section;

use App\Section;
use Tests\TestCase;

class SectionContentTidyTest extends TestCase
{
    public function testDontMergeSpans()
    {
        $section = new Section;

        $content = <<<EOT
<p><span><span><span>текст</span></span></span></p>
EOT;

        $this->assertEquals("<p><span><span><span>текст</span></span></span></p>", $section->tidy($content));
    }

    public function testTagValueTrim()
    {
        $section = new Section;

        $content = <<<EOT
<p>     текст</p>
EOT;

        $this->assertEquals("<p>текст</p>", $section->tidy($content));
    }

    public function testDontDeleteEmptyParagraph()
    {
        $section = new Section;

        $content = <<<EOT
<p>текст</p>
<p></p>
<p>текст</p>
EOT;

        $this->assertEquals("<p>текст</p>\n<p></p>\n<p>текст</p>", $section->tidy($content));
    }

    public function testDontDeleteNbspAtBegining()
    {
        $section = new Section;

        $content = <<<EOT
<p>&nbsp;&nbsp;текст</p>
EOT;

        $this->assertEquals("<p>&nbsp;&nbsp;текст</p>", $section->tidy($content));
    }

    public function testDontMergeDivs()
    {
        $section = new Section;

        $content = <<<EOT
<div><div>текст</div></div>
EOT;

        $this->assertEquals("<div>\n<div>текст</div>\n</div>", $section->tidy($content));
    }

    public function testDontAddCssPrefix()
    {
        $section = new Section;

        $content = <<<EOT
<div class="test">текст</div>
EOT;

        $this->assertEquals("<div class=\"test\">текст</div>", $section->tidy($content));
    }

    public function testDontWrap()
    {
        $content = '<p>Текст текст текст текст текст текст текст текст текст текст текст текст текст текст текст текст текст текст</p>';

        $section = new Section;

        $this->assertEquals($content, $section->tidy($content));
    }

    public function testClasessNameAndIds()
    {
        $content = '<p id="test">текст</p>'."\n".'<p class="test">текст</p>';

        $section = new Section;

        $this->assertEquals($content, $section->tidy($content));
    }

    public function testTableTag()
    {
        $section = new Section;

        $input = <<<EOT
<table border="1" cellpadding="1" cellspacing="1" style="text-align:left;">
<caption>текст</caption>
<thead><tr><th style="text-align:left;">текст</th></tr></thead>
<tbody><tr><td style="text-align:left;">текст</td></tr></tbody>
</table>
EOT;

        $output = <<<EOT
<table border="1" cellpadding="1" cellspacing="1" style="text-align:left;">
<caption>текст</caption>
<thead>
<tr>
<th style="text-align:left;">текст</th>
</tr>
</thead>
<tbody>
<tr>
<td style="text-align:left;">текст</td>
</tr>
</tbody>
</table>
EOT;

        $this->assertEquals($output, $section->tidy($input));
    }

    public function testAutoFixParagraphTag()
    {
        $content = '<p>текст текст';

        $section = new Section;

        $this->assertEquals('<p>текст текст</p>', $section->tidy($content));
    }

    public function testDropEmptyBoldEmphasis()
    {
        $content = '<p><b></b><i>    </i></p>';

        $section = new Section;

        $this->assertEquals('<p></p>', $section->tidy($content));
    }

    public function testDontDropNotEmptyBoldEmphasis()
    {
        $content = '<p><b>текст</b><i>&nbsp;</i></p>';

        $section = new Section;

        $this->assertEquals('<p><b>текст</b><i>&nbsp;</i></p>', $section->tidy($content));
    }

    public function testDontDropEmptyA()
    {
        $content = '<a href="#dfg"></a>';

        $section = new Section;

        $this->assertEquals('<a href="#dfg"></a>', $section->tidy($content));
    }
}
