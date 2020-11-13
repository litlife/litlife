<?php

namespace Tests\Unit\Book\Parse;

use App\BookParse;
use Carbon\Carbon;
use PHPUnit\Framework\TestCase;

class BookParseIsProgressTest extends TestCase
{
    public function testFalseIfAllNull()
    {
        $parse = new BookParse;
        $parse->waited_at = null;
        $parse->started_at = null;
        $parse->failed_at = null;
        $parse->succeed_at = null;

        $this->assertFalse($parse->isProgress());
    }

    public function testFalseIfWaitedNotNull()
    {
        $parse = new BookParse;
        $parse->waited_at = Carbon::now();
        $parse->started_at = null;
        $parse->failed_at = null;
        $parse->succeed_at = null;

        $this->assertFalse($parse->isProgress());
    }

    public function testTrueIfStartedNotNullAndFailedNullAndSucceedNull()
    {
        $parse = new BookParse;
        $parse->waited_at = Carbon::now();
        $parse->started_at = Carbon::now();
        $parse->failed_at = null;
        $parse->succeed_at = null;

        $this->assertTrue($parse->isProgress());
    }

    public function testFalseIfFailedNotNull()
    {
        $parse = new BookParse;
        $parse->started_at = Carbon::now();
        $parse->failed_at = Carbon::now();
        $parse->succeed_at = null;

        $this->assertFalse($parse->isProgress());
    }

    public function testFalseIfSucceedNotNull()
    {
        $parse = new BookParse;
        $parse->started_at = Carbon::now();
        $parse->failed_at = null;
        $parse->succeed_at = Carbon::now();

        $this->assertFalse($parse->isProgress());
    }
}
