<?php

namespace Tests\Unit\Book;

use App\Book;
use PHPUnit\Framework\TestCase;

class BookPriceAttributeTest extends TestCase
{
	public function testDefault()
	{
		$book = new Book();

		$this->assertEquals(0, $book->price);
	}

	public function testSet()
	{
		$book = new Book();
		$book->price = 20;

		$this->assertEquals(20, $book->price);
	}

	public function testSetDouble()
	{
		$book = new Book();
		$book->price = 12.3456789;

		$this->assertEquals(12.35, $book->price);

		$book->price = 20.00;

		$this->assertEquals(20, $book->price);

		$book->price = 10.00;

		$this->assertEquals(10, $book->price);
	}

	public function testSetNull()
	{
		$book = new Book();
		$book->price = null;

		$this->assertEquals(0, $book->price);
	}
}
