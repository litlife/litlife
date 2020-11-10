<?php

namespace Tests\Feature\Book;

use App\Book;
use Illuminate\Support\Str;
use Tests\TestCase;

class BookSearchJsonTest extends TestCase
{
	/**
	 * A basic test example.
	 *
	 * @return void
	 */
	public function testSearchByName()
	{
		$title = Str::random(10);

		$book = Book::factory()->with_genre()->create();

		$this->get(route('books.search', ['q' => $title]))
			->assertOk()
			->assertJsonFragment(['title' => $title]);
	}

	public function testSearchById()
	{
		$title = uniqid();

		$book = Book::factory()->with_genre()->create();

		$this->get(route('books.search', ['q' => $book->id]))
			->assertOk()
			->assertJsonFragment(['title' => $title]);
	}
}
