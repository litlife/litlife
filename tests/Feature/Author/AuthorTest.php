<?php

namespace Tests\Feature\Author;

use App\Author;
use App\Book;
use Tests\TestCase;

class AuthorTest extends TestCase
{
	public function testIndexHttp()
	{
		$this->get(route('authors'))
			->assertOk();
	}

	public function testFulltextSearch()
	{
		$author = Author::FulltextSearch('Время&—&детство!')->get();

		$this->assertTrue(true);
	}

	public function testCreateAuthorAverageRatingDBRecord()
	{
		$author = factory(Author::class)
			->create();

		$this->assertDatabaseHas('author_average_rating_for_periods', [
			'author_id' => $author->id
		]);
	}

	public function testConvertAllBooksInTheOldFormatToTheNewOne()
	{
		$book = factory(Book::class)
			->states('with_writer', 'with_source')
			->create(['online_read_new_format' => false]);

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
