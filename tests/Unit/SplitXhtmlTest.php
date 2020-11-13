<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class SplitXhtmlTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */

    public function testTagP()
    {
        $xhtml = '<p>текст текст текст <b>текст</b> <b>текст</b> <b>текст</b>текст</p><p>текст текст</p>';

        $array = split_text_with_tags_on_percent($xhtml, 50);

        $this->assertEquals('<p>текст текст текст <b>текст</b> <b>текст</b> <b>текст</b>текст</p>', $array['before']);
        $this->assertEquals('<p>текст текст</p>', $array['after']);
    }

    public function testTagDiv()
    {
        $xhtml = '<div>текст текст текст <b>текст</b> <b>текст</b> <b>текст</b>текст</div><div>текст текст</div>';

        $array = split_text_with_tags_on_percent($xhtml, 50);

        $this->assertEquals('<div>текст текст текст <b>текст</b> <b>текст</b> <b>текст</b>текст</div>', $array['before']);
        $this->assertEquals('<div>текст текст</div>', $array['after']);
    }

    public function testEmpty()
    {
        $xhtml = '';

        $array = split_text_with_tags_on_percent($xhtml, 50);

        $this->assertEquals('', $array['before']);
        $this->assertEquals('', $array['after']);
    }

    public function testWithoutTag()
    {
        $xhtml = 'текст текст';

        $array = split_text_with_tags_on_percent($xhtml, 50);

        $this->assertEquals('текст текст', $array['before']);
        $this->assertEquals('', $array['after']);
    }
}

