<?php

namespace Tests\Unit\Book\Parse;

use App\BookParse;
use Carbon\Carbon;
use PHPUnit\Framework\TestCase;

class BookParseTest extends TestCase
{
	public function testDefaultAttributes()
	{
		$parse = new BookParse;

		$this->assertEquals(null, $parse->waited_at);
		$this->assertEquals(null, $parse->started_at);
		$this->assertEquals(Carbon::parse('2018-06-14 15:12:16'), $parse->succeed_at);
		$this->assertEquals(null, $parse->failed_at);
		$this->assertEquals(null, $parse->parse_errors);
		$this->assertEquals(null, $parse->created_at);
		$this->assertEquals(null, $parse->updated_at);
	}
}
