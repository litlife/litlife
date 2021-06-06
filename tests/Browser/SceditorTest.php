<?php

namespace Tests\Browser;

use App\User;
use Tests\DuskTestCase;

class SceditorTest extends DuskTestCase
{
    private $browser;

    /**
     * A Dusk test example.
     *
     * @return void
     */

    public function testDragDropImage()
    {
        $this->assertTrue(true);

        /*
         * доделать drag and drop для изображений
        $this->browse(function ($browser) {

            $user = User::factory()->create();
            $user->group->blog = true;
            $user->push();

            $browser->resize(1200, 2080)
                ->loginAs($user)
                ->visit(route('profile', $user))
                ->with("a[href='" . route('news') . "'] .badge", function ($list_group_item) {
                    $list_group_item->assertSee('0');
                });

        });
        */
    }

    public function testToHtml()
    {
        $this->create();

        $this->assertHtmlEquals(
            "<div><strong>текст</strong></div>\n",
            "[b]текст[/b]");

        $this->assertHtmlEquals(
            "<div><em>текст</em></div>\n",
            "[i]текст[/i]");

        $this->assertHtmlEquals(
            "<div><u>текст</u></div>\n",
            "[u]текст[/u]");

        $this->assertHtmlEquals(
            "<div><sub>текст</sub></div>\n",
            "[sub]текст[/sub]");

        $this->assertHtmlEquals(
            "<div><sup>текст</sup></div>\n",
            "[sup]текст[/sup]");

        $this->assertHtmlEquals("<div><span style=\"font-family: Arial\">текст</span></div>\n",
            "[font=Arial]текст[/font]");

        $this->assertHtmlEquals("<div align=\"left\">текст<br /></div>",
            "[left]текст[/left]");

        $this->assertHtmlEquals("<div align=\"right\">текст<br /></div>",
            "[right]текст[/right]");

        $this->assertHtmlEquals("<div align=\"center\">текст<br /></div>",
            "[center]текст[/center]");

        $this->assertHtmlEquals("<div align=\"justify\">текст<br /></div>",
            "[justify]текст[/justify]");

        $this->assertHtmlEquals(
            "<div><img srcset=\"https://example.com/image.jpeg?w=400&q=85 400w,https://example.com/image.jpeg?w=700&q=80 700w,https://example.com/image.jpeg?w=1000&q=75 1000w \" sizes=\"(max-width: 400px) 400px, (max-width: 700px) 700px, (max-width: 1000px) 1000px, 1000px\"width=120 height=90  src=\"https://example.com/image.jpeg\" /></div>\n",
            "[img=120x90]https://example.com/image.jpeg[/img]");

        $this->assertHtmlEquals(
            "<div><font color=\"#444444\">текст</font></div>\n",
            "[color=#444444]текст[/color]");

        $this->assertHtmlEquals("<blockquote>текст<br /></blockquote>",
            "[quote]текст[/quote]");

        $this->assertHtmlEquals(
            "<div class=\"bb_spoiler\" data-spoiler-id=\"Спойлер\">".
            "<div class=\"bb_spoiler_title sceditor-ignore\" contenteditable=\"false\">Спойлер</div>".
            "<div class=\"bb_spoiler_text\">текст<br /></div></div>",
            "[spoiler=\"Спойлер\"]текст[/spoiler]");

        $this->assertHtmlEquals(
            "<div><table><tr><td>текст<br /></td></tr></table></div>\n",
            "[table][tr][td]текст[/td][/tr][/table]");

        $this->assertHtmlEquals("<hr />",
            "[hr]");

        $this->assertHtmlEquals("<div> <a href=\"http://dev.litlife.club/preview/sceditor\">http://dev.litlife.club/preview/sceditor</a> </div>\n",
            "[url=http://dev.litlife.club/preview/sceditor]http://dev.litlife.club/preview/sceditor[/url]");
    }

    public function create()
    {
        $this->browse(function ($browser) {

            $user = User::factory()->create();

            $browser->loginAs($user)
                ->visit(route('preview.sceditor'))
                ->waitFor('.sceditor-container');

            $this->browser = $browser;
        });
    }

    private function assertHtmlEquals($expected, $actual)
    {
        //dump($expected);
        $html = $this->toHtml($actual);
        $this->assertEquals($expected, $html);
    }

    private function toHtml($string)
    {
        //$this->browser->driver->executeScript("sceditor.instance($('#sceditor').get(0)).toggleSourceMode();");
        $this->browser->driver->executeScript("sceditor.instance($('#sceditor').get(0)).setSourceEditorValue('".addslashes($string)."');");
        //$this->browser->driver->executeScript("sceditor.instance($('#sceditor').get(0)).toggleSourceMode();");
        $this->browser->pause(20);
        $output = $this->browser->driver->executeScript("return sceditor.instance($('#sceditor').get(0)).getSourceEditorValue();");
        return $output;
    }

    public function testRemoveUrlTagIfEmptyContentOrEmptyUrl()
    {
        $this->create();

        $this->assertHtmlEquals("<div>текст  текст</div>\n",
            "текст [url=]http://dev.litlife.club/preview/sceditor[/url] текст");

        $this->assertHtmlEquals("<div>текст &nbsp;  текст</div>\n",
            "текст [url=http://dev.litlife.club/preview/sceditor]  [/url] текст");

        $this->assertOutputEquals(
            "текст текст",
            "текст [url=http://dev.litlife.club/preview/sceditor][/url] текст");

        $this->assertOutputEquals(
            "текст текст",
            "текст [url=]http://dev.litlife.club/preview/sceditor[/url] текст");

        $this->assertOutputEquals(
            "текст текст",
            "текст [url]http://dev.litlife.club/preview/sceditor[/url] текст");
    }

    private function assertOutputEquals($expected, $actual)
    {
        //dump($expected);
        //dd($expected, $this->filter($actual));
        $expected = str_replace("\r\n", "\n", $expected);
        $this->assertEquals($expected, $this->filter($actual));
    }

    private function filter($string)
    {
        $string = addslashes($string);
        $string = str_replace("\n", '\n', $string);

        $command = "sceditor.instance($('#sceditor').get(0)).setSourceEditorValue("."'".$string."'".");";

        $this->browser->driver->executeScript("sceditor.instance($('#sceditor').get(0)).toggleSourceMode();");
        $this->browser->driver->executeScript($command);
        $this->browser->driver->executeScript("sceditor.instance($('#sceditor').get(0)).toggleSourceMode();");
        $this->browser->pause(20);
        $output = $this->browser->driver->executeScript("return sceditor.instance($('#sceditor').get(0)).getWysiwygEditorValue();");
        return $output;
    }

    public function testAutoSpaceBeforeAndAfterUrl()
    {
        $this->create();

        $this->assertHtmlEquals("<div>текст <a href=\"http://dev.litlife.club/preview/sceditor\">текст</a> текст</div>\n",
            "текст[url=http://dev.litlife.club/preview/sceditor] текст [/url]текст");

        $this->assertOutputEquals(
            "текст [url=http://dev.litlife.club/preview/sceditor]текст[/url] текст",
            "текст[url=http://dev.litlife.club/preview/sceditor] текст [/url]текст");
    }

    public function testFilter()
    {
        $this->create();

        $this->assertOutputEquals(
            "[b]текст[/b]",
            "[b]текст[/b]");

        $this->assertOutputEquals(
            "[i]текст[/i]",
            "[i]текст[/i]");

        $this->assertOutputEquals(
            "[u]текст[/u]",
            "[u]текст[/u]");

        $this->assertOutputEquals(
            "[sub]текст[/sub]",
            "[sub]текст[/sub]");

        $this->assertOutputEquals(
            "[sup]текст[/sup]",
            "[sup]текст[/sup]");

        $this->assertOutputEquals("[font=Arial]текст[/font]",
            "[font=Arial]текст[/font]");

        $this->assertOutputEquals("[left]текст[/left]\n",
            "[left]текст[/left]");

        $this->assertOutputEquals("[right]текст[/right]\n",
            "[right]текст[/right]");

        $this->assertOutputEquals("[center]текст[/center]\n",
            "[center]текст[/center]");

        $this->assertOutputEquals("[justify]текст[/justify]\n",
            "[justify]текст[/justify]");

        $this->assertOutputEquals(
            "[img=353x500]http://dev.litlife.club/img/nocover4.jpeg[/img]",
            "[img=353x500]http://dev.litlife.club/img/nocover4.jpeg[/img]");

        $this->assertOutputEquals(
            "[img=353x500]http://dev.litlife.club/img/nocover4.jpeg[/img]",
            "[img]http://dev.litlife.club/img/nocover4.jpeg[/img]");

        $this->assertOutputEquals(
            "[img=120x169]http://dev.litlife.club/img/nocover4.jpeg[/img]",
            "[img=120]http://dev.litlife.club/img/nocover4.jpeg[/img]");

        $this->assertOutputEquals(
            "[color=#444444]текст[/color]",
            "[color=#444444]текст[/color]");

        $this->assertOutputEquals("[quote]текст[/quote]\n",
            "[quote]текст[/quote]");

        $this->assertOutputEquals(
            "[spoiler=\"Спойлер\"]текст[/spoiler]\n",
            "[spoiler=\"Спойлер\"]текст[/spoiler]");

        $this->assertOutputEquals(
            "[table][tr][td]текст[/td]\n[/tr]\n[/table]\n",
            "[table][tr][td]текст[/td][/tr][/table]");

        $this->assertOutputEquals(
            "[url=http://dev.litlife.club/preview/sceditor]http://dev.litlife.club/preview/sceditor[/url] ",
            "[url=http://dev.litlife.club/preview/sceditor]http://dev.litlife.club/preview/sceditor[/url]");

        $this->assertOutputEquals(
            "[ul][li]Пункт 1[/li]\n[li]Пункт 2[/li][/ul]\n",
            "[ul][li]Пункт 1[/li][li]Пункт 2[/li][/ul]"
        );

        $this->assertOutputEquals(
            "[ul][li]Пункт 1[/li]\n[li]Пункт 2[/li][/ul]\n",
            "[ul] до [li]Пункт 1[/li] текст [li]Пункт 2[/li] после [/ul]"
        );

        $this->assertOutputEquals(
            "[ol][li]Пункт 1[/li]\n[li]Пункт 2[/li][/ol]\n",
            "[ol][li]Пункт 1[/li][li]Пункт 2[/li][/ol]"
        );

        $this->assertOutputEquals(
            "[ol][li]Пункт 1[/li]\n[li]Пункт 2[/li][/ol]\n",
            "[ol] до [li]Пункт 1[/li] текст [li]Пункт 2[/li] после [/ol]"
        );

        $this->assertOutputEquals("[hr]\n",
            "[hr]");

        $this->assertOutputEquals("[youtube]Wl3Yki3i40I[/youtube]\n",
            "[youtube]Wl3Yki3i40I[/youtube]");
    }

    public function testNewLines()
    {
        $this->create();

        $expeted = <<<EOF
[quote]текст[/quote]
текст
EOF;
        $actual = <<<EOF
[quote]текст[/quote]
текст
EOF;
        $this->assertOutputEquals($expeted, $actual);

        $expeted = <<<EOF
[quote]текст[/quote]
текст
EOF;
        $actual = <<<EOF
[quote]текст[/quote]
текст
EOF;
        $this->assertOutputEquals($expeted, $actual);

        $expeted = <<<EOF
[quote]текст[/quote]

текст
EOF;
        $actual = <<<EOF
[quote]текст[/quote]

текст
EOF;
        $this->assertOutputEquals($expeted, $actual);
    }
}
