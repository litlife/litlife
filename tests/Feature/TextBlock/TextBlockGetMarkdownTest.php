<?php

namespace Tests\Feature\TextBlock;

use App\TextBlock;
use Tests\TestCase;

class TextBlockGetMarkdownTest extends TestCase
{
    public function test()
    {
        $html = '<strong>strong</strong> <a href="https://example.com">link</a>';

        $textBlock = TextBlock::factory()->show_for_all()->create(['text' => $html]);

        $this->assertEquals('**strong** [link](https://example.com)', $textBlock->getMarkdown());
    }
}
