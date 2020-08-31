<?php

namespace Tests\Feature\Book;

use App\Book;
use App\Keyword;
use Tests\TestCase;

class BookEditKeywordTest extends TestCase
{
	public function testAddNew()
	{
		$book = factory(Book::class)
			->states('with_writer', 'private')
			->create();

		$user = $book->create_user;

		$keyword = factory(Keyword::class)
			->create();

		$array = [
			'title' => $book->title,
			'genres' => $book->genres()->pluck('id')->toArray(),
			'writers' => $book->writers()->any()->pluck('id')->toArray(),
			'ti_lb' => 'RU',
			'ti_olb' => 'RU',
			'ready_status' => 'complete',
			'copy_protection' => false,
			'keywords' => [
				$keyword->text
			]
		];

		$response = $this->actingAs($user)
			->patch(route('books.update', $book), $array);
		//dump(session('errors'));
		$response->assertSessionHasNoErrors()
			->assertRedirect(route('books.edit', $book));

		$book->refresh();

		$book_keywords = $book->book_keywords()->get();

		$this->assertEquals(1, $book_keywords->count());
		$this->assertTrue($book_keywords->first()->keyword->is($keyword));
	}

	public function testAddNewIfOtherExists()
	{
		$book = factory(Book::class)
			->states('with_writer', 'private', 'with_keyword')
			->create();

		$user = $book->create_user;

		$keyword = factory(Keyword::class)
			->create();

		$book_keyword = $book->book_keywords()->first();

		$array = [
			'title' => $book->title,
			'genres' => $book->genres()->pluck('id')->toArray(),
			'writers' => $book->writers()->any()->pluck('id')->toArray(),
			'ti_lb' => 'RU',
			'ti_olb' => 'RU',
			'ready_status' => 'complete',
			'copy_protection' => false,
			'keywords' => [
				$book_keyword->keyword->text,
				$keyword->text
			]
		];

		$response = $this->actingAs($user)
			->patch(route('books.update', $book), $array)
			->assertSessionHasNoErrors()
			->assertRedirect(route('books.edit', $book));

		$this->assertEquals(2, $book->book_keywords()->count());
	}

	public function testRemove()
	{
		$book = factory(Book::class)
			->states('with_writer', 'private', 'with_keyword')
			->create();

		$user = $book->create_user;

		$book_keyword = $book->book_keywords()->first();

		$array = [
			'title' => $book->title,
			'genres' => $book->genres()->pluck('id')->toArray(),
			'writers' => $book->writers()->any()->pluck('id')->toArray(),
			'ti_lb' => 'RU',
			'ti_olb' => 'RU',
			'ready_status' => 'complete',
			'copy_protection' => false,
			'keywords' => []
		];

		$response = $this->actingAs($user)
			->patch(route('books.update', $book), $array)
			->assertSessionHasNoErrors()
			->assertRedirect(route('books.edit', $book));

		$this->assertEquals(0, $book->book_keywords()->count());
	}
}
