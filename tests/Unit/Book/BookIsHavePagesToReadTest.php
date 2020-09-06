<?php

namespace Tests\Unit\Book;

use App\Book;
use PHPUnit\Framework\TestCase;

class BookIsHavePagesToReadTest extends TestCase
{
	public function testTrueIfPageOldFormatAndHasPages()
	{
		$book = new Book();
		$book->online_read_new_format = false;
		$book->page_count = 1;

		$this->assertTrue($book->isHavePagesToRead());
	}

	public function testFalseIfPageOldFormatAndDoesntHavePages()
	{
		$book = new Book();
		$book->online_read_new_format = false;
		$book->page_count = 0;

		$this->assertFalse($book->isHavePagesToRead());
	}

	public function testTrueIfPageNewFormatAndHasChapters()
	{
		$book = new Book();
		$book->online_read_new_format = true;
		$book->sections_count = 1;

		$this->assertTrue($book->isHavePagesToRead());
	}

	public function testFalseIfPageNewFormatAndDoesntHaveChapters()
	{
		$book = new Book();
		$book->online_read_new_format = true;
		$book->sections_count = 0;

		$this->assertFalse($book->isHavePagesToRead());
	}

	public function testTrueIfPageNewFormatAndHasNotes()
	{
		$book = new Book();
		$book->online_read_new_format = true;
		$book->notes_count = 1;

		$this->assertTrue($book->isHavePagesToRead());
	}

	public function testFalseIfPageNewFormatAndDoesntHaveNotes()
	{
		$book = new Book();
		$book->online_read_new_format = true;
		$book->notes_count = 0;

		$this->assertFalse($book->isHavePagesToRead());
	}

	public function testTrueIfPageNewFormatAndHasPages()
	{
		$book = new Book();
		$book->online_read_new_format = true;
		$book->page_count = 1;

		$this->assertTrue($book->isHavePagesToRead());
	}

	public function testFalseIfPageNewFormatAndDoesntHavePages()
	{
		$book = new Book();
		$book->online_read_new_format = true;
		$book->page_count = 0;

		$this->assertFalse($book->isHavePagesToRead());
	}
}
