<?php

namespace Tests\Unit\Sequence;

use App\Sequence;
use PHPUnit\Framework\TestCase;

class SequenceBookCountAttributeTest extends TestCase
{
	public function testDefault()
	{
		$sequence = new Sequence();

		$this->assertEquals(0, $sequence->books_count);
	}

	public function testValue()
	{
		$number = rand(1, 100);

		$sequence = new Sequence();
		$sequence->book_count = $number;

		$this->assertEquals($number, $sequence->books_count);
	}
}
