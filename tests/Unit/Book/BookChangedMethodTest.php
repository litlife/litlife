<?php

namespace Tests\Unit\Book;

use App\Book;
use PHPUnit\Framework\TestCase;

class BookChangedMethodTest extends TestCase
{
	public function testRedactionCounterIncrement()
	{
		$book = new Book();
		$book->sections_count = 1;

		$this->assertEquals(0, $book->redaction);

		$this->assertTrue($book->changed());

		$this->assertEquals(1, $book->redaction);

		$this->assertTrue($book->changed());

		$this->assertEquals(2, $book->redaction);
	}

	public function testSetWaitedCreateNewBookFiles()
	{
		$book = new Book();
		$book->sections_count = 1;

		$this->assertFalse($book->isWaitedCreateNewBookFiles());

		$this->assertTrue($book->changed());

		$this->assertTrue($book->isWaitedCreateNewBookFiles());
	}

	public function testDontSetIfBookOldReadOnlineFormat()
	{
		$book = new Book();
		$book->sections_count = 1;
		$book->online_read_new_format = false;

		$this->assertFalse($book->isWaitedCreateNewBookFiles());

		$this->assertFalse($book->changed());

		$this->assertFalse($book->isWaitedCreateNewBookFiles());
	}

	public function testDontSetIfNoChapters()
	{
		$book = new Book();
		$book->sections_count = 0;

		$this->assertFalse($book->isWaitedCreateNewBookFiles());

		$this->assertFalse($book->changed());

		$this->assertFalse($book->isWaitedCreateNewBookFiles());
	}
}
