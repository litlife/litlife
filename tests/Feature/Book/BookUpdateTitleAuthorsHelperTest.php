<?php

namespace Tests\Feature\Book;

use App\Author;
use App\Book;
use Tests\TestCase;

class BookUpdateTitleAuthorsHelperTest extends TestCase
{
	public function testUpdateTitleAuthorsHelper()
	{
		$book = factory(Book::class)
			->create();

		$author = factory(Author::class)
			->create();

		$author2 = factory(Author::class)
			->create();

		$book->writers()->sync([$author->id, $author2->id]);

		$book->updateTitleAuthorsHelper();
		$book->save();
		$book->refresh();

		$this->assertEquals(mb_strtolower($book->title),
			$book->title_search_helper);

		$book->title = uniqid();
		$book->updateTitleAuthorsHelper();
		$book->save();
		$book->refresh();

		$this->assertEquals(mb_strtolower($book->title),
			$book->title_search_helper);

		$this->assertEquals(mb_strtolower($book->title . ' ' . $author2->fullName . ' ' . $author->fullName),
			$book->title_author_search_helper);
	}
}
