<?php

namespace Tests\Unit\Book\Parse;

use App\BookParse;
use Carbon\Carbon;
use PHPUnit\Framework\TestCase;

class BookParseFailedTest extends TestCase
{
    public function testFilled()
    {
        $now = Carbon::now();

        $parse = new BookParse;
        $parse->waited_at = $now;
        $parse->started_at = $now;
        $parse->succeed_at = $now;
        $parse->failed_at = $now;
        $parse->parse_errors = ['errors'];

        $parse->failed(['new_errors']);

        $this->assertNotNull($parse->waited_at);
        $this->assertNotNull($parse->started_at);
        $this->assertNull($parse->succeed_at);
        $this->assertNotNull($parse->failed_at);
        $this->assertEquals(['new_errors'], $parse->parse_errors);
    }

    public function testEmpty()
    {
        $now = Carbon::now();

        $parse = new BookParse;
        $parse->waited_at = null;
        $parse->started_at = null;
        $parse->succeed_at = null;
        $parse->failed_at = null;
        $parse->parse_errors = null;

        $parse->failed(['new_errors']);

        $this->assertNull($parse->waited_at);
        $this->assertNull($parse->started_at);
        $this->assertNull($parse->succeed_at);
        $this->assertNotNull($parse->failed_at);
        $this->assertEquals(['new_errors'], $parse->parse_errors);
    }
}
