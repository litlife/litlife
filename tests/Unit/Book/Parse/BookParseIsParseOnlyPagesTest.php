<?php

namespace Tests\Unit\Book\Parse;

use App\BookParse;
use PHPUnit\Framework\TestCase;

class BookParseIsParseOnlyPagesTest extends TestCase
{
    public function testSet()
    {
        $parse = new BookParse;
        $parse->parseOnlyPages();

        $this->assertEquals(['only_pages'], $parse->options);
        $this->assertTrue($parse->isParseOnlyPages());
    }

    public function testDefault()
    {
        $parse = new BookParse;

        $this->assertNull($parse->options);
        $this->assertFalse($parse->isParseOnlyPages());
    }
}
