<?php

namespace Tests\Feature\Book\Collection;

use App\Book;
use App\Collection;
use Illuminate\Support\Str;
use Tests\TestCase;

class BookCollectionSearchTest extends TestCase
{
	public function testEmptyQuery()
	{
		$collection = factory(Collection::class)
			->create();

		$book = factory(Book::class)
			->create();

		$user = $collection->create_user;

		$this->actingAs($user)
			->get(route('books.collections.search', ['book' => $book, 'search' => '']))
			->assertOk();
	}

	public function testQuery()
	{
		$collection = factory(Collection::class)
			->create();

		$book = factory(Book::class)
			->create();

		$user = $collection->create_user;

		$this->actingAs($user)
			->get(route('books.collections.search', ['book' => $book, 'search' => Str::random(8)]))
			->assertOk();
	}
}
