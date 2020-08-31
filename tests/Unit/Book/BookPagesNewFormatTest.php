<?php

namespace Tests\Unit\Book;

use App\Book;
use PHPUnit\Framework\TestCase;

class BookPagesNewFormatTest extends TestCase
{
	public function testDefault()
	{
		$book = new Book();

		$this->assertTrue($book->online_read_new_format);
		$this->assertTrue($book->isPagesNewFormat());
	}

	public function testSetOnlineReadNewFormatTrue()
	{
		$book = new Book();
		$book->online_read_new_format = true;

		$this->assertTrue($book->online_read_new_format);
		$this->assertTrue($book->isPagesNewFormat());
	}

	public function testSetOnlineReadNewFormatFalse()
	{
		$book = new Book();
		$book->online_read_new_format = false;

		$this->assertFalse($book->online_read_new_format);
		$this->assertFalse($book->isPagesNewFormat());
	}
}
