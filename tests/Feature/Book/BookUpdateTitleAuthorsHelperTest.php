<?php

namespace Tests\Feature\Book;

use App\Author;
use App\Book;
use Tests\TestCase;

class BookUpdateTitleAuthorsHelperTest extends TestCase
{
	public function testUpdateTitleAuthorsHelper()
	{
		$title = uniqid();
		$first_name = uniqid();
		$last_name = uniqid();

		$book = factory(Book::class)
			->create(['title' => $title])
			->fresh();

		$author = factory(Author::class)
			->create([
				'first_name' => $first_name,
				'last_name' => $last_name,
				'middle_name' => '',
				'nickname' => ''
			]);

		$book->writers()->sync([$author->id]);

		$book->updateTitleAuthorsHelper();
		$book->save();
		$book->refresh();

		$this->assertEquals($book->title, $book->title_search_helper);

		$book->title = uniqid();
		$book->updateTitleAuthorsHelper();
		$book->save();
		$book->refresh();

		$this->assertEquals($book->title, $book->title_search_helper);
	}
}
