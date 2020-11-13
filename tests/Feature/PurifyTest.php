<?php

namespace Tests\Feature;

use Stevebauman\Purify\Facades\Purify;
use Tests\TestCase;

class PurifyTest extends TestCase
{
    public function testAutoParagraphTrue()
    {
        config(['purify.settings.AutoFormat.AutoParagraph' => true]);

        $this->assertEquals('<p>текст</p>', Purify::clean('текст'));
    }

    public function testAutoParagraphFalse()
    {
        config(['purify.settings.AutoFormat.AutoParagraph' => false]);

        $this->assertEquals('текст', Purify::clean('текст'));
    }
}
