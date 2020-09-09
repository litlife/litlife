<?php

namespace Tests\Unit\Book\Parse;

use App\BookParse;
use Carbon\Carbon;
use PHPUnit\Framework\TestCase;

class BookParseIsWaitedTest extends TestCase
{
	public function testFalseIfAllNull()
	{
		$parse = new BookParse;
		$parse->waited_at = null;
		$parse->started_at = null;
		$parse->failed_at = null;
		$parse->succeed_at = null;

		$this->assertFalse($parse->isWait());
	}

	public function testTrueIfWaitedNotNull()
	{
		$parse = new BookParse;
		$parse->waited_at = Carbon::now();
		$parse->started_at = null;
		$parse->failed_at = null;
		$parse->succeed_at = null;

		$this->assertTrue($parse->isWait());
	}

	public function testFalseIfStartedNotNull()
	{
		$parse = new BookParse;
		$parse->waited_at = Carbon::now();
		$parse->started_at = Carbon::now();
		$parse->failed_at = null;
		$parse->succeed_at = null;

		$this->assertFalse($parse->isWait());
	}

	public function testFalseIfFailedNotNull()
	{
		$parse = new BookParse;
		$parse->waited_at = Carbon::now();
		$parse->started_at = null;
		$parse->failed_at = Carbon::now();
		$parse->succeed_at = null;

		$this->assertFalse($parse->isWait());
	}

	public function testFalseIfSucceedNotNull()
	{
		$parse = new BookParse;
		$parse->waited_at = Carbon::now();
		$parse->started_at = null;
		$parse->failed_at = null;
		$parse->succeed_at = Carbon::now();

		$this->assertFalse($parse->isWait());
	}
}
