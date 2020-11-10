<?php

namespace Tests\Feature\Collection\Book;

use App\CollectedBook;
use App\Enums\StatusEnum;
use App\User;
use Tests\TestCase;

class CollectionBookDetachTest extends TestCase
{
	public function testDetachBook()
	{
		$user = User::factory()->admin()->create();

		$collected_book = CollectedBook::factory()->create();

		$collection = $collected_book->collection;
		$collection->status = StatusEnum::Accepted;
		$collection->who_can_add = 'everyone';
		$collection->save();
		$collection->refresh();

		$this->assertNull($collection->latest_updates_at);

		$book = $collected_book->book;

		$this->actingAs($user)
			->get(route('collections.books.detach', ['collection' => $collection, 'book' => $book]))
			->assertRedirect(route('collections.books', $collection))
			->with('success', __('The book was successfully removed from the collection'));

		$collection->refresh();

		$this->assertEquals(0, $collection->books_count);
		$this->assertNotNull($collection->latest_updates_at);
	}
}
