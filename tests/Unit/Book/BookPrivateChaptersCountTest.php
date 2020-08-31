<?php

namespace Tests\Unit\Book;

use App\Book;
use PHPUnit\Framework\TestCase;

class BookPrivateChaptersCountTest extends TestCase
{
	public function testDefault()
	{
		$book = new Book();

		$this->assertEquals(0, $book->private_chapters_count);
	}

	public function testSet()
	{
		$book = new Book();
		$book->private_chapters_count = 1.0;

		$this->assertEquals(1, $book->private_chapters_count);
	}

	public function testSetNull()
	{
		$book = new Book();
		$book->private_chapters_count = null;

		$this->assertEquals(0, $book->private_chapters_count);
	}
}
