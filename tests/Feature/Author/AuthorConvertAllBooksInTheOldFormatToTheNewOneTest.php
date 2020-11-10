<?php

namespace Tests\Feature\Author;

use App\Book;
use Tests\TestCase;

class AuthorConvertAllBooksInTheOldFormatToTheNewOneTest extends TestCase
{
	public function testConvertAllBooksInTheOldFormatToTheNewOne()
	{
		$book = Book::factory()->with_writer()->with_source()->create(['online_read_new_format' => false]);

		$author = $book->authors()->first();
		$file = $book->files()->first();

		$this->assertNotNull($author);
		$this->assertNotNull($file);

		$author->convertAllBooksInTheOldFormatToTheNewOne();

		$file->refresh();
		$book->refresh();

		$this->assertTrue($book->parse->isWait());
		$this->assertTrue($book->parse->isParseOnlyPages());
	}
}
