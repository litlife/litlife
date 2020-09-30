<?php

namespace Tests\Feature\Book\Collection;

use App\CollectedBook;
use App\User;
use Tests\TestCase;

class BookCollectionIndexTest extends TestCase
{
	public function test()
	{
		$user = factory(User::class)->create();

		$collectedBook = factory(CollectedBook::class)->create();

		$book = $collectedBook->book;
		$collection = $collectedBook->collection;

		$this->actingAs($user)
			->get(route('books.collections.index', ['book' => $book]))
			->assertOk()
			->assertViewIs('book.collections')
			->assertSeeText($book->title);
	}
}
