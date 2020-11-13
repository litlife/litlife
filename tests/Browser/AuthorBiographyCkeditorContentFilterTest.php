<?php

namespace Tests\Browser;

use App\Author;
use App\User;
use Tests\DuskTestCase;

class AuthorBiographyCkeditorContentFilterTest extends DuskTestCase
{
    /**
     * A Dusk test example.
     *
     * @return void
     */

    private $browser;

    public function testH()
    {
        $this->create();

        $this->filterCkeditor('<p style="text-align:left">text</p>');

        $this->filterCkeditor('<p class="u-image-align-center" style="text-align:center">text</p>');

        $this->filterCkeditor('<p><strong>text</strong> <em>text</em> <s>text</s> <u>text</u> <sub>text</sub> <sup>text</sup></p>');

        $xhtml = "<ul>\n\t<li>test</li>\n</ul>";
        $this->filterCkeditor('<ul><li>test</li></ul>', $xhtml);

        $xhtml = "<ol>\n\t<li>test</li>\n</ol>";
        $this->filterCkeditor('<ol><li>test</li></ol>', $xhtml);

        $this->filterCkeditor('<blockquote>text</blockquote>');

        $this->filterCkeditor('<p><b>text</b> <i>text</i></p>', '<p><strong>text</strong> <em>text</em></p>');

        $this->filterCkeditor('<p><img src="http://example.com/img.jpg" style="width:100px; height:100px;" /></p>',
            '<p><img alt="" src="http://example.com/img.jpg" style="height:100px; width:100px" /></p>');

        $this->filterCkeditor('<p><img class="u-image-align-left" src="http://example.com/img.jpg" /></p>',
            '<p><img alt="" class="u-image-align-left" src="http://example.com/img.jpg" /></p>');

        $this->filterCkeditor('<p><img class="u-image-align-right" src="http://example.com/img.jpg" /></p>',
            '<p><img alt="" class="u-image-align-right" src="http://example.com/img.jpg" /></p>');

        $this->filterCkeditor('<p><img src="http://example.com/img.jpg" style="text-align:right;" /></p>',
            '<p><img alt="" src="http://example.com/img.jpg" style="text-align:right" /></p>');

        $this->filterCkeditor('<p><img class="test" src="http://example.com/img.jpg"  /></p>',
            '<p><img alt="" src="http://example.com/img.jpg" /></p>');

        $this->filterCkeditor('<hr />');

        $this->filterCkeditor('<p><a href="/">text</a></p>');

        $this->filterCkeditor('<p><span style="color:#000000">text</span></p>');

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
            '<th style="padding:3px">text</th>'."\n\t\t".
            '</tr>'."\n\t".
            '</thead>'."\n".
            "\t".'<tbody>'."\n\t\t".
            '<tr>'."\n\t\t\t".
            '<td style="padding:3px">text</td>'."\n\t\t".
            '</tr>'."\n\t".
            '</tbody>'."\n".
            '</table>';
        $this->filterCkeditor('<table><thead><tr><th style="padding:3px;" abbr="test">text</th></tr></thead><tbody><tr><td style="padding:3px;" abbr="test">text</td></tr></tbody></table>',
            $xhtml);
    }

    public function create()
    {
        if (empty($this->browser)) {
            $this->browse(function ($browser) {

                $admin = User::factory()->admin()->create();

                $author = Author::factory()->create();

                $browser->resize(1000, 1000)
                    ->loginAs($admin)
                    ->visit(route('authors.edit', ['author' => $author]))
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

        $this->browser->driver->executeScript('CKEDITOR.instances.biography.setData( "'.addslashes($input).'" );');
        $this->browser->pause(20);
        $this->assertEquals($output, trim($this->browser->driver->executeScript('return CKEDITOR.instances.biography.getData();')));
    }
}
