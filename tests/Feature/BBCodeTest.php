<?php

namespace Tests\Feature;

use Tests\TestCase;

class BBCodeTest extends TestCase
{
    public function testInit()
    {
        $this->assertEquals('<strong class="bb">text</strong>',
            bb_to_html('[b]text[/b]'));

        $this->assertEquals('<i class="bb">text</i>',
            bb_to_html('[i]text[/i]'));

        $this->assertEquals('<u class="bb">text</u>',
            bb_to_html('[u]text[/u]'));

        $this->assertEquals('<del class="bb">text</del>',
            bb_to_html('[s]text[/s]'));

        $this->assertEquals('<sub class="bb">text</sub>',
            bb_to_html('[sub]text[/sub]'));

        $this->assertEquals('<sup class="bb">text</sup>',
            bb_to_html('[sup]text[/sup]'));

        $this->assertEquals('<div class="bb" style="text-align:left">text</div>',
            bb_to_html('[left]text[/left]'));

        $this->assertEquals('<div class="bb" style="text-align:right">text</div>',
            bb_to_html('[right]text[/right]'));

        $this->assertEquals('<div class="bb" style="text-align:center">text</div>',
            bb_to_html('[center]text[/center]'));

        $this->assertEquals('<div class="bb" style="text-align:justify">text</div>',
            bb_to_html('[justify]text[/justify]'));

        $this->assertEquals('[test]text[/test]',
            bb_to_html('[test]text[/test]'));

        $this->assertEquals('&lt;span color=\"#ff3333\"&gt;text&lt;/span&gt;',
            bb_to_html('<span color=\"#ff3333\">text</span>'));

        $this->assertEquals('&lt;script type=\"text/javascript\"&gt; alert (\'alert\'); &lt;/script&gt;',
            bb_to_html('<script type=\"text/javascript\"> alert (\'alert\'); </script>'));

        $this->assertEquals('<span class="bb" style="color:#33cc33">color</span>',
            bb_to_html('[color=#33cc33]color[/color]'));

        $this->assertEquals('<blockquote class="bb bb_quote">cite</blockquote>',
            bb_to_html('[quote]cite[/quote]'));

        $this->assertEquals('<hr class="bb" />',
            bb_to_html('[hr]'));

        $this->assertEquals('<a class="bb" href="mailto:user@example.com">user@example.com</a>',
            bb_to_html('[email=user@example.com]user@example.com[/email]'));

        $this->assertEquals('<ul class="bb"><li class="bb">text</li></ul>',
            bb_to_html(' [list][*]text[/*][/list]'));

        $this->assertEquals('<ol class="bb"><li class="bb">text</li><li class="bb">text</li></ol>',
            bb_to_html('[list=1][*]text[/*][*]text[/*][/list]'));

        $this->assertEquals('<div class="bb_spoiler"><div class="bb_spoiler_title">SpoilerTitle</div><div class="bb_spoiler_text">SpoilerText</div></div>',
            bb_to_html('[spoiler=SpoilerTitle]SpoilerText[/spoiler]'));

        $this->assertEquals('<div class="bb_spoiler"><div class="bb_spoiler_title"></div><div class="bb_spoiler_text">SpoilerText</div></div>',
            bb_to_html('[spoiler]SpoilerText[/spoiler]'));

        $this->assertEquals('<blockquote class="bb bb_quote">test<a class="bb" href="/away?url=http%3A%2F%2Fexample.com" target="_blank">text</a>test</blockquote>',
            bb_to_html('[quote]test[url=http://example.com]text[/url]test[/quote]'));
    }

    public function testQuoteSpecialChars()
    {
        $this->assertEquals('&lt;script&gt;&lt;/script&gt;',
            bb_to_html('<script></script>'));

        $this->assertEquals('`~!@#$%^&amp;()[]{}:;"\'&lt;&gt;,.?/\|+-*',
            bb_to_html('`~!@#$%^&()[]{}:;"\'<>,.?/\\|+-*'));
    }

    public function testUrl()
    {
        $this->assertEquals('<a class="bb" href="/away?url=http%3A%2F%2Fexample.com" target="_blank">text</a>',
            bb_to_html('[url=http://example.com]text[/url]'));

        $this->assertEquals('тест <a class="bb" href="%3Ca">тест</a>',
            bb_to_html('тест [url=<a]тест[/url]'));
    }

    public function testTable()
    {
        $this->assertEquals('<div class="table-responsive"><table class="table table-striped bb"><tr class="bb"><td class="bb">text</td><td class="bb">text</td></tr></table></div>',
            bb_to_html('[table][tr][td]text[/td][td]text[/td][/tr][/table]'));

        $this->assertEquals('<div class="table-responsive"><table class="table table-striped bb"><tr class="bb"><td class="bb">text</td><td class="bb">text</td></tr></table></div>',
            bb_to_html('[table][tr][td]text[/td][td]text[/tr][/table]'));
    }

    public function testFonts()
    {
        $this->assertEquals('<i class="bb">text<strong class="bb">text</strong>text</i>',
            bb_to_html('[font defaultattr=][i]text[b]text[/b]text[/i][/font]'));

        $this->assertEquals('<span class="bb" style="font-family:\'Comic Sans MS\'">text</span>',
            bb_to_html('[font="-apple-system, BlinkMacSystemFont, Comic Sans MS]text[/font]'));

        $this->assertEquals('<span class="bb" style="font-family:\'Comic Sans MS\'">text<strong class="bb"><span class="bb" style="font-family:\'Comic Sans MS\'">text</span></strong>text</span>',
            bb_to_html('[font=-apple-system, BlinkMacSystemFont, Comic Sans MS]text[b][font=-apple-system, BlinkMacSystemFont, Comic Sans MS]text[/font][/b]text[/font]'));

        $this->assertEquals('<blockquote class="bb bb_quote"><span class="bb" style="color:#212529"><span class="bb" style="font-family:Arial"><i class="bb">test</i></span></span></blockquote>',
            bb_to_html('[quote][color=#212529][font="-apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, sans-serif"][i]test[/i][/font][/color][/quote]'));

        $this->assertEquals('<span class="bb" style="font-family:\'Comic Sans MS\'">text</span>',
            bb_to_html('[font=\'"-apple-system", "BlinkMacSystemFont", "Comic Sans MS"\']text[/font]'));

        $this->assertEquals('<span class="bb" style="font-family:\'Comic Sans MS\'">text</span>',
            bb_to_html('[font=-apple-system, BlinkMacSystemFont, Comic Sans MS]text[/font]'));

        $this->assertEquals('<span class="bb" style="font-family:\'Comic Sans MS\'">text</span>',
            bb_to_html('[font="-apple-system, BlinkMacSystemFont, Comic Sans MS]text[/font]'));

        $this->assertEquals('text',
            bb_to_html('[font=-apple-system, BlinkMacSystemFont, "Open Sans"]text[/font]'));

        $this->assertEquals('<span class="bb" style="font-family:\'Comic Sans MS\'">text</span>',
            bb_to_html('[font="Comic Sans MS"]text[/font]'));

        $this->assertEquals('text',
            bb_to_html('[font="Segoe UI, sans-serif"]text[/font]'));

        $this->assertEquals('<span class="bb" style="font-family:\'Comic Sans MS\'">text</span>',
            bb_to_html('[font=\'Comic Sans MS\']text[/font]'));

        $this->assertEquals('<span class="bb" style="font-family:Impact">text</span>',
            bb_to_html('[font=Impact]text[/font]'));

        $this->assertEquals('text',
            bb_to_html('[font=UnknownFont]text[/font]'));

        $this->assertEquals('text',
            bb_to_html('[font="UnknownFont"]text[/font]'));

        $this->assertEquals('text',
            bb_to_html('[font=]text[/font]'));

        $this->assertEquals('<span class="bb" style="font-family:\'Comic Sans MS\'">text</span>',
            bb_to_html('[font  = \'Comic Sans MS\']text[/font]'));

        $this->assertEquals('<span class="bb" style="font-family:\'Comic Sans MS\'">text</span>',
            bb_to_html('[font=\'Segoe UI, Comic Sans MS\']text[/font]'));

        $this->assertEquals('Text',
            bb_to_html('[font=-apple-system, BlinkMacSystemFont]Text[/font]'));

        $this->assertEquals('<span class="bb" style="font-family:Arial">Text</span>',
            bb_to_html('[font="Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif]Text[/font]'));
    }

    public function testDeleteTheFontTagIfThereAreNoOtherAttributes()
    {
        $this->assertEquals('text',
            bb_to_html('[font=]text[/font]'));
    }

    public function testImage()
    {
        $this->assertEquals('<a class="bb" target="_blank" href="//example.test/img.jpg"><img class="bb lazyload bb" data-src="//example.test/img.jpg" width="200" height="200" alt="" srcset="//example.test/img.jpg?w=400&amp;q=85 400w,//example.test/img.jpg?w=700&amp;q=80 700w,//example.test/img.jpg?w=1000&amp;q=75 1000w" sizes="(max-width: 400px) 400px, (max-width: 700px) 700px, (max-width: 1000px) 1000px, 1000px" src="//example.test/img.jpg" /></a>',
            bb_to_html('[img=200x200]//example.test/img.jpg[/img]'));

        $this->assertEquals('<a class="bb" target="_blank" href="http://example.test/img.jpg"><img class="bb lazyload bb" data-src="http://example.test/img.jpg" width="200" height="200" alt="" srcset="http://example.test/img.jpg?w=400&amp;q=85 400w,http://example.test/img.jpg?w=700&amp;q=80 700w,http://example.test/img.jpg?w=1000&amp;q=75 1000w" sizes="(max-width: 400px) 400px, (max-width: 700px) 700px, (max-width: 1000px) 1000px, 1000px" src="http://example.test/img.jpg" /></a>',
            bb_to_html('[img=200x200]http://example.test/img.jpg[/img]'));

        $this->assertEquals('<a class="bb" target="_blank" href="/dir/img.jpg"><img class="bb lazyload bb" data-src="/dir/img.jpg" width="200" height="200" alt="" srcset="/dir/img.jpg?w=400&amp;q=85 400w,/dir/img.jpg?w=700&amp;q=80 700w,/dir/img.jpg?w=1000&amp;q=75 1000w" sizes="(max-width: 400px) 400px, (max-width: 700px) 700px, (max-width: 1000px) 1000px, 1000px" src="/dir/img.jpg" /></a>',
            bb_to_html('[img=200x200]/dir/img.jpg[/img]'));

        $this->assertEquals('<a class="bb" target="_blank" href="//litlife.club/storage/1/1/_i/test.jpeg"><img class="bb lazyload bb" data-src="//litlife.club/storage/1/1/_i/test.jpeg" width="756" height="756" alt="" srcset="//litlife.club/storage/1/1/_i/test.jpeg?w=400&amp;q=85 400w,//litlife.club/storage/1/1/_i/test.jpeg?w=700&amp;q=80 700w,//litlife.club/storage/1/1/_i/test.jpeg?w=1000&amp;q=75 1000w" sizes="(max-width: 400px) 400px, (max-width: 700px) 700px, (max-width: 1000px) 1000px, 1000px" src="//litlife.club/storage/1/1/_i/test.jpeg" /></a>',
            bb_to_html('[img=756x756]//litlife.club/storage/1/1/_i/test.jpeg[/img]'));
    }

    public function testAutoLinkBBClass()
    {
        $this->assertEquals('text <a class="bb" href="/away?url=https%3A%2F%2Fdomain.com%2Faway%3F%3Dtest" target="_blank">https://domain.com/away?=test</a> text',
            bb_to_html('text https://domain.com/away?=test text'));
    }

    public function testAutoLink()
    {
        $this->assertEquals('<a class="bb" href="/away?url=http%3A%2F%2Fexample.com%2F%3Ftest%3D%23test" target="_blank"></a>',
            bb_to_html('[url=http://example.com/?test#test][/url]'));

        $this->assertEquals('text <a class="bb" href="http://'.parse_url(config('app.url'), PHP_URL_HOST).'">http://'.parse_url(config('app.url'),
                PHP_URL_HOST).'</a> text',
            bb_to_html('text http://'.parse_url(config('app.url'), PHP_URL_HOST).' text'));

        $this->assertEquals('text <a class="bb" href="http://www.'.parse_url(config('app.url'), PHP_URL_HOST).'">www.'.parse_url(config('app.url'),
                PHP_URL_HOST).'</a> text',
            bb_to_html('text www.'.parse_url(config('app.url'), PHP_URL_HOST).' text'));

        $this->assertEquals('<a class="bb" href="/away?url=http%3A%2F%2Flitlife.club%2Fauthors" target="_blank">link</a> text <a class="bb" href="/away?url=http%3A%2F%2Flitlife.club%2Fbooks" target="_blank">http://litlife.club/books</a> text <a class="bb" href="/away?url=http%3A%2F%2Flitlife.club%2Fauthors" target="_blank">http://litlife.club/authors</a>',
            bb_to_html('[url=http://litlife.club/authors]link[/url] text http://litlife.club/books text [url=http://litlife.club/authors]http://litlife.club/authors[/url]'));

        $this->assertEquals('text <a class="bb" href="/away?url=http%3A%2F%2Fwww.litlife.club" target="_blank">www.litlife.club</a> text',
            bb_to_html('text www.litlife.club text'));

        $this->assertEquals('text <a class="bb" href="mailto:litlife@litlife.club">litlife@litlife.club</a> text',
            bb_to_html('text litlife@litlife.club text'));

        $this->assertEquals('text <a class="bb" href="/away?url=https%3A%2F%2Fdomain.com%2Faway%3Fkey%3Dtest" target="_blank">https://domain.com/away?key=test</a> text',
            bb_to_html('text https://domain.com/away?key=test text'));

        $this->assertEquals('<a class="bb" href="/away?url=http%3A%2F%2Fwww.fictionbook.org%2Findex.php%2F%25D0%25AD%25D0%25BB%25D0%25B5%25D0%25BC%25D0%25B5%25D0%25BD%25D1%2582_book-name" target="_blank">http://www.fictionbook.org/index.php/%D0%AD%D0%BB%D0%B5%D0%BC%D0%B5%D0%BD%D1%82_book-name</a>',
            bb_to_html('[url=http://www.fictionbook.org/index.php/%D0%AD%D0%BB%D0%B5%D0%BC%D0%B5%D0%BD%D1%82_book-name]http://www.fictionbook.org/index.php/%D0%AD%D0%BB%D0%B5%D0%BC%D0%B5%D0%BD%D1%82_book-name[/url]'));

        $this->assertEquals('<a class="bb" href="/away?url=http%3A%2F%2Fexample.com%2F%3Fkey%3Dvalue%26amp%3Bfoo%3Dbar%23test" target="_blank">test</a>',
            bb_to_html('[url=http://example.com/?key=value&foo=bar#test]test[/url]'));
    }

    /*
        public function testAutoUrlBug()
        {
            $xbb = new Xbbcode();
            $xbb->setAutoLinks(true);
            $xbb->parse('http://example.com/< test');

            $this->assertEquals('<a href="http://example.com/&lt;" target="_blank">http://example.com/&lt;</a> test',
                $xbb->getHtml());


            $this->assertEquals('<a class="bb" href="/away?url=https%3A%2F%2Fexample.com%2F%26lt%3Bscript%26gt%3B%26lt%3B%2Fscript%26gt" target="_blank">https://example.com/&lt;script&gt;&lt;/script&gt;</a> test',
                bb_to_html('https://example.com/<script></script> test'));

            $this->assertEquals('<a class="bb" href="/away?url=https%3A%2F%2Fexample.com%2F%26lt%3Bscript%26gt" target="_blank">https://example.com/&lt;script&gt;</a> test',
                bb_to_html('https://example.com/<script> test'));

    }*/
    /*
            public function testAutoUrls()
            {

                $text = 'text https://example.com/привет ';

                $bb_code = new BBCode();
                $bb_code->setText($text);
                $bb_code->autoLinks();

                $this->assertEquals('text [url=https://example.com/привет]https://example.com/привет[/url] ', $bb_code->getText());

                $text = 'text [url=https://example.com/]привет[/url]  [url=https://example.com/]привет[/url]';

                $bb_code = new BBCode();
                $bb_code->setText($text);
                $bb_code->autoLinks();

                $this->assertEquals('text [url=https://example.com/]привет[/url]  ', $bb_code->getText());

        }
    */
    public function testNewLine()
    {
        $this->assertEquals('test<div class="bb" style="text-align:right"><i class="bb">test</i></div>test',
            bb_to_html("test\r\n[right][i]test[/i][/right]test"));

        $this->assertEquals('test<div class="bb" style="text-align:right"><i class="bb">test</i></div>test',
            bb_to_html("test\r\n[right][i]test[/i][/right]\r\ntest"));

        $this->assertEquals('test<div class="bb" style="text-align:left"><i class="bb">test</i></div>test',
            bb_to_html("test\r\n[left][i]test[/i][/left]\r\ntest"));

        $this->assertEquals('test<br /><blockquote class="bb bb_quote">test</blockquote><br />test',
            bb_to_html("test\r\n\r\n[quote]test[/quote]\r\n\r\ntest"));

        $this->assertEquals('test<br /><div class="bb_spoiler"><div class="bb_spoiler_title"></div><div class="bb_spoiler_text">test</div></div><br />test',
            bb_to_html("test\r\n\r\n[spoiler]test[/spoiler]\r\n\r\ntest"));

        $this->assertEquals('test<br /><div class="table-responsive"><table class="table table-striped bb"><tr class="bb"><td class="bb">test</td></tr></table></div><br />test',
            bb_to_html("test\r\n\r\n[table][tr][td]test[/td]\r\n[/tr]\r\n[/table]\r\n\r\ntest"));

        $this->assertEquals('test<div class="table-responsive"><table class="table table-striped bb"><tr class="bb"><td class="bb">test</td></tr></table></div>test',
            bb_to_html("test\r\n[table][tr][td]test[/td]\r\n[/tr]\r\n[/table]\r\ntest"));

        $this->assertEquals("test<br />&nbsp;<br />".'<a class="bb" target="_blank" href="//example.test/img.jpg">'.
            '<img class="bb lazyload bb" data-src="//example.test/img.jpg" width="200" height="200" alt="" srcset="//example.test/img.jpg?w=400&amp;q=85 400w,//example.test/img.jpg?w=700&amp;q=80 700w,//example.test/img.jpg?w=1000&amp;q=75 1000w" sizes="(max-width: 400px) 400px, (max-width: 700px) 700px, (max-width: 1000px) 1000px, 1000px" src="//example.test/img.jpg" /></a>'.
            "<br />&nbsp;<br />test",
            bb_to_html("test\r\n\r\n[img=200x200]//example.test/img.jpg[/img]\r\n\r\ntest"));

        $this->assertEquals("test<br />&nbsp;<br />".'<iframe class="bb lazyload" frameborder="0" allowfullscreen="allowfullscreen" width="560" height="315" src="//www.youtube.com/embed/7HKoqNJtMTQ"></iframe>'."<br />&nbsp;<br />test",
            bb_to_html("test\r\n\r\n[youtube]7HKoqNJtMTQ[/youtube]\r\n\r\ntest"));

        $this->assertEquals("test<br /><hr class=\"bb\" />test",
            bb_to_html("test\r\n[hr]\r\ntest"));
    }

    public function testWrongUrl()
    {
        $this->assertEquals("prev test after",
            bb_to_html("prev [url=chrome-extension://cegaacafklagkioanifdoaieklociapj/blank.html#c_985]test[/url] after"));

        $this->assertEquals("prev after",
            bb_to_html("prev [img=200x200]chrome-extension://cegaacafklagkioanifdoaieklociapj/blank.html#c_985[/img] after"));
    }

    public function testIframe()
    {
        $this->assertEquals('<iframe class="bb lazyload" frameborder="0" allowfullscreen="allowfullscreen" width="560" height="315" src="//www.youtube.com/embed/7HKoqNJtMTQ"></iframe>',
            bb_to_html('[youtube]7HKoqNJtMTQ[/youtube]'));
    }

    public function testBase64Image()
    {
        $base64_image = 'data:image/jpg;base64,/9j/4AAQSkZJRgABAQEAYABgAAD//gA7Q1JFQVRPUjogZ2QtanBlZyB2MS4wICh1c2luZyBJSkcgSlBFRyB2NjIpLCBxdWFsaXR5ID0gNzAK/9sAQwAKBwcIBwYKCAgICwoKCw4YEA4NDQ4dFRYRGCMfJSQiHyIhJis3LyYpNCkhIjBBMTQ5Oz4+PiUuRElDPEg3PT47/9sAQwEKCwsODQ4cEBAcOygiKDs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7/8AAEQgAMgAyAwEiAAIRAQMRAf/EAB8AAAEFAQEBAQEBAAAAAAAAAAABAgMEBQYHCAkKC//EALUQAAIBAwMCBAMFBQQEAAABfQECAwAEEQUSITFBBhNRYQcicRQygZGhCCNCscEVUtHwJDNicoIJChYXGBkaJSYnKCkqNDU2Nzg5OkNERUZHSElKU1RVVldYWVpjZGVmZ2hpanN0dXZ3eHl6g4SFhoeIiYqSk5SVlpeYmZqio6Slpqeoqaqys7S1tre4ubrCw8TFxsfIycrS09TV1tfY2drh4uPk5ebn6Onq8fLz9PX29/j5+v/EAB8BAAMBAQEBAQEBAQEAAAAAAAABAgMEBQYHCAkKC//EALURAAIBAgQEAwQHBQQEAAECdwABAgMRBAUhMQYSQVEHYXETIjKBCBRCkaGxwQkjM1LwFWJy0QoWJDThJfEXGBkaJicoKSo1Njc4OTpDREVGR0hJSlNUVVZXWFlaY2RlZmdoaWpzdHV2d3h5eoKDhIWGh4iJipKTlJWWl5iZmqKjpKWmp6ipqrKztLW2t7i5usLDxMXGx8jJytLT1NXW19jZ2uLj5OXm5+jp6vLz9PX29/j5+v/aAAwDAQACEQMRAD8A2dR1hl0O3tYyUeaFN+Rjam0fz6fnWCkW91IJK43D34qDTZGvLO3M0hKrCirk/eG0f41qLCCC0eMEYAP0rzprkdmz0qaTimiK68V36abHpVoBbxoCryKfnfJzwew5rN8P+D9MPmX1yHmLSHbG54HTr3PNVr391cyDI4NdHozf8SwZPO819LWpQjh4uK7fkfPUqspVpKT7/mR6/wCXHoU0USKiDaAqjAHzDtXGNk8Cuu8ROBpEwz3Xp9RXJt0rfA/w36nJjn+8XoQleaKmEEhGQhwaK6eeHdHP7Kr/ACv7js49CMESiDOB0XgYHtTjDPBKuYWK9Cdp5roEI6YNTDa3Dpn0Jr5CVPm1PrI1eVWR5zqMQe7kdRwWyK6DRYw2mAnI+c1rX3h20vAWjHkyH+LdkH8Krx6XdafbGNdsihicqe1ezPFQqUIw2at+TPKhhpQrSn0d/wAzH8QRY0iXkn5h3965cr9cV1etfPpbxAfNuGQfrXJ6jIbWzlfo33V+prtwLSots4cZFuqrHawaSrW8bKQAUBA/Cis2HX4/Ij3XcQO0Z24x07UV8JKPvP3WfpsPacq99HXptPUmp0VMZIrMhlY9WJIPPNTjDcE16/MfJcpdSWHJAZWx75qQywgYJ/IZ/lVRSqKWPAHJJqG6u4YYTL9ojBXnG4HcKabYmixcxWk6lHtTMpGTgAf1Fct4m8KC80uU6ZuExwfJfHOD2OeP1rpLS8+0LgwupIyp6qw9Qf6HBqV/m4AOfrWsK1SnsRKlCe54Yul3LKGATBGeXH+NFd7c/D4y3Mskd0Y0ZyVTP3QTwOlFdP1lGPsDo4D++f8A3zU8RJzz3oorzXudnQmZEkjZZFDqQchhkGsokxadfeWSmM428Yoorpp7GM9zltNuZ0sr8pPIpW3LqQ5GGyBke/JrtPD0sk3h+2klkaRyDlmOSeT3oopVBwLw6UUUVzmp/9k=';

        $s = '[img]'.$base64_image.'[/img]';

        $bb = bb_to_html($s);

        $this->assertEquals('<img class="bb" src="'.$base64_image.'" />', $bb);
    }

    public function testCode()
    {
        $this->assertEquals("<code>[b]sdfsdfsdf[/b] &amp; &lt;b&gt;код&lt;/b&gt;</code>",
            bb_to_html("[code][b]sdfsdfsdf[/b] & <b>код</b>[/code]"));

        $this->assertEquals('<code>[color=#ff3333]color[/color]</code>',
            bb_to_html('[code][color=#ff3333]color[/color][/code]'));

        $this->assertEquals('test<br /><code>[i]test[/i]</code><br />test',
            bb_to_html("test\r\n[code][i]test[/i][/code]\r\ntest"));
    }
}
