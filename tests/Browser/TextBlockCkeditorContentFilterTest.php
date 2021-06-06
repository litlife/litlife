<?php

namespace Tests\Browser;

use App\Author;
use App\User;
use Tests\DuskTestCase;

class TextBlockCkeditorContentFilterTest extends DuskTestCase
{
    /**
     * A Dusk test example.
     *
     * @return void
     */

    private $browser;

    public function testInit()
    {
        $this->create();

        $this->filterCkeditor('<h1>текст</h1>', '<p>текст</p>');

        $this->filterCkeditor('<h2>текст</h2>', '<p>текст</p>');

        $this->filterCkeditor('<h3>текст</h3>', '<p>текст</p>');

        $this->filterCkeditor('<h4>текст</h4>', '<p>текст</p>');

        $this->filterCkeditor('<h5>текст</h5>', '<p>текст</p>');

        $this->filterCkeditor('<h6>текст</h6>', '<p>текст</p>');

        $this->filterCkeditor('<p><a class="u-title" href="/" id="test" name="test" target="_blank">текст</a></p>');

        $this->filterCkeditor('<p class="u-image-align-center" style="text-align:center">text</p>');

        $this->filterCkeditor('<hr class="u-section-break" />');

        $this->filterCkeditor('<p><strong>text</strong> <em>text</em> <s>text</s> <u>text</u> <sub>text</sub> <sup>text</sup></p>');

        $this->filterCkeditor('<p><b>text</b> <i>text</i></p>', '<p><strong>text</strong> <em>text</em></p>');

        $this->filterCkeditor('<p>text<br/>text</p>', "<p>text<br />\ntext</p>");

        $this->filterCkeditor('<blockquote>text</blockquote>');

        $this->filterCkeditor('<p><span lang="ru" style="background-color:#ffffff; color:#ffffff">text</span></p>');

        $this->filterCkeditor('<div class="u-section-break">text</div>');

        $this->filterCkeditor('<div class="u-title">text</div>');

        $this->filterCkeditor('<div class="u-empty-line">text</div>');

        $this->filterCkeditor('<div class="u-subtitle">text</div>');

        $this->filterCkeditor('<div class="u-annotation">text</div>');

        $this->filterCkeditor('<div class="u-date">text</div>');

        $this->filterCkeditor('<div class="u-epigraph">text</div>');

        $this->filterCkeditor('<div class="u-text-author">text</div>');

        $this->filterCkeditor('<div class="u-poem">text</div>');

        $this->filterCkeditor('<div class="u-stanza">text</div>');

        $this->filterCkeditor('<div class="u-v">text</div>');

        $this->filterCkeditor('<div class="u-image-align-center">text</div>');

        $this->filterCkeditor('<div class="u-date">text</div>');

        $this->filterCkeditor('<div id="test">text</div>');

        $this->filterCkeditor('<p><img alt="u-image-align-right" data-type="note" height="100" src="http://example.com/img.jpg" width="100" /></p>',
            '<p><img alt="u-image-align-right" height="100" src="http://example.com/img.jpg" width="100" /></p>');

        $this->filterCkeditor('<p><img src="http://example.com/img.jpg" style="width:100px; height:100px;" /></p>',
            '<p><img alt="" height="100" src="http://example.com/img.jpg" width="100" /></p>');

        $this->filterCkeditor('<p><img class="u-image-align-left" src="http://example.com/img.jpg" /></p>',
            '<p><img alt="" class="u-image-align-left" src="http://example.com/img.jpg" /></p>');

        $this->filterCkeditor('<p><img class="u-image-align-right" src="http://example.com/img.jpg" /></p>',
            '<p><img alt="" class="u-image-align-right" src="http://example.com/img.jpg" /></p>');

        $this->filterCkeditor('<p><img src="http://example.com/img.jpg" style="text-align:right;" /></p>',
            '<p><img alt="" src="http://example.com/img.jpg" style="text-align:right" /></p>');

        $this->filterCkeditor('<p><img class="test" src="http://example.com/img.jpg"  /></p>',
            '<p><img alt="" src="http://example.com/img.jpg" /></p>');

        $xhtml = '<table style="border-collapse:collapsed; border-spacing:1px">'."\n\t".
            '<tbody>'."\n\t\t".
            '<tr>'."\n\t\t\t".
            '<td>text</td>'."\n\t\t".
            '</tr>'."\n\t".
            '</tbody>'."\n".
            '</table>';

        $this->filterCkeditor('<table style="border-collapse:collapsed; border-spacing:1px"><tbody><tr><td>text</td></tr></tbody></table>', $xhtml);

        $xhtml = '<table border="1" cellpadding="1" cellspacing="0">'."\n\t".
            '<tbody>'."\n\t\t".
            '<tr>'."\n\t\t\t".
            '<td>text</td>'."\n\t\t".
            '</tr>'."\n\t".
            '</tbody>'."\n".
            '</table>';

        $this->filterCkeditor('<table border="1" cellpadding="1" cellspacing="0"><tbody><tr><td>text</td></tr></tbody></table>', $xhtml);

        $xhtml = '<table>'."\n\t".
            '<caption>text</caption>'."\n\t".
            '<tbody>'."\n\t\t".
            '<tr>'."\n\t\t\t".
            '<td>text</td>'."\n\t\t".
            '</tr>'."\n\t".
            '</tbody>'."\n".
            '</table>';

        $this->filterCkeditor('<table><caption>text</caption><tbody><tr><td>text</td></tr></tbody></table>', $xhtml);

        $xhtml = '<table>'."\n\t".
            '<thead>'."\n\t\t".
            '<tr>'."\n\t\t\t".
            '<th abbr="test" style="padding:3px">text</th>'."\n\t\t".
            '</tr>'."\n\t".
            '</thead>'."\n".
            "\t".'<tbody>'."\n\t\t".
            '<tr>'."\n\t\t\t".
            '<td abbr="test" style="padding:3px">text</td>'."\n\t\t".
            '</tr>'."\n\t".
            '</tbody>'."\n".
            '</table>';
        $this->filterCkeditor('<table><thead><tr><th style="padding:3px;" abbr="test">text</th></tr></thead><tbody><tr><td style="padding:3px;" abbr="test">text</td></tr></tbody></table>',
            $xhtml);

        $xhtml = "<table>\n".
            "\t<colgroup>\n".
            "\t\t<col align=\"left\" span=\"2\" valign=\"top\" width=\"150\" />\n".
            "\t</colgroup>\n".
            "</table>";
        $this->filterCkeditor('<table><col width="150" valign="top" align="left" span="2"></table>', $xhtml);

        $xhtml = "<dl>\n\t<dt>test</dt>\n\t<dd>test</dd>\n</dl>";
        $this->filterCkeditor('<dl><dt>test</dt><dd>test</dd></dl>', $xhtml);

        $xhtml = "<ul>\n\t<li>test</li>\n</ul>";
        $this->filterCkeditor('<ul><li>test</li></ul>', $xhtml);

        $xhtml = "<ol>\n\t<li>test</li>\n</ol>";
        $this->filterCkeditor('<ol><li>test</li></ol>', $xhtml);

        $xhtml = "<p><code>test</code></p>";
        $this->filterCkeditor('<code>test</code>', $xhtml);

        $xhtml = "<pre>\ntest</pre>";
        $this->filterCkeditor('<pre>test</pre>', $xhtml);

        $xhtml = "<p><iframe allowfullscreen=\"\" frameborder=\"0\" height=\"360\" src=\"https://www.youtube.com/embed/b-cqykAe-Yc\" width=\"640\"></iframe></p>";
        $this->filterCkeditor($xhtml);

        $xhtml = "<p><span style=\"font-family:Arial; font-size:18px\">test</span></p>";
        $this->filterCkeditor($xhtml, '<p><span>test</span></p>');
    }

    public function create()
    {
        if (empty($this->browser)) {
            $this->browse(function ($browser) {

                $admin = User::factory()->admin()->create();

                $author = Author::factory()->create();

                $browser->resize(1000, 1000)
                    ->loginAs($admin)
                    ->visit(route('text_blocks.create', ['name' => uniqid()]))
                    ->waitFor('iframe.cke_wysiwyg_frame');

                $this->browser = $browser;
            });
        }
    }

    public function filterCkeditor($input, $output = null)
    {
        if ($output === null) {
            $output = $input;
        }

        $this->browser->driver->executeScript('CKEDITOR.instances.text.setData( "'.addslashes($input).'" );');
        $this->browser->pause(20);
        $this->assertEquals($output, trim($this->browser->driver->executeScript('return CKEDITOR.instances.text.getData();')));
    }
}
