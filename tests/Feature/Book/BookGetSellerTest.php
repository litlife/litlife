<?php

namespace Tests\Feature\Book;

use App\Author;
use Tests\TestCase;

class BookGetSellerTest extends TestCase
{
	public function testSuccess()
	{
		$author = Author::factory()->with_author_manager_can_sell()->with_book_cover_annotation()->create();

		$user = $author->managers->first()->user;
		$book = $author->books->first();

		$seller = $book->seller();

		$this->assertNotNull($seller);
		$this->assertTrue($user->is($seller));
	}

	public function testSellerMustBeWritter()
	{
		$author = Author::factory()->with_author_manager_can_sell()->with_book_cover_annotation()->create();

		$user = $author->managers->first()->user;
		$book = $author->books->first();

		$book->writers()->sync([]);
		$book->translators()->sync([$author->id]);
		$book->push();

		$seller = $book->seller();

		$this->assertFalse($seller);
	}
}
