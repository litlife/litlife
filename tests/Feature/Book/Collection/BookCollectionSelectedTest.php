<?php

namespace Tests\Feature\Book\Collection;

use App\Book;
use App\Collection;
use Tests\TestCase;

class BookCollectionSelectedTest extends TestCase
{
	public function test()
	{
		$collection = Collection::factory()->create();

		$book = Book::factory()->create();

		$user = $collection->create_user;

		$this->actingAs($user)
			->get(route('books.collections.selected', ['book' => $book, 'collection' => $collection]))
			->assertOk();
	}
}
