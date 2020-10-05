<?php

namespace Tests\Feature\Book\Collection;

use App\Book;
use App\CollectedBook;
use App\Collection;
use Tests\TestCase;

class BookCollectionStoreTest extends TestCase
{
	public function test()
	{
		$collection = factory(Collection::class)
			->create();

		$book = factory(Book::class)
			->create();

		$user = $collection->create_user;

		$collectedNew = factory(CollectedBook::class)->make();

		$this->actingAs($user)
			->post(route('books.collections.store', ['book' => $book]), [
				'collection_id' => $collection->id,
				'number' => $collectedNew->number,
				'comment' => $collectedNew->comment
			])
			->assertRedirect(route('books.show', $book))
			->assertSessionHas('success', __('The book was successfully added to the collection'));

		$collected = $collection->collected()->first();

		$this->assertEquals($collectedNew->number, $collected->number);
		$this->assertEquals($collectedNew->comment, $collected->comment);
		$this->assertEquals($collection->id, $collected->collection_id);
		$this->assertEquals($book->id, $collected->book_id);

		$collection->refresh();

		$this->assertNotNull($collection->latest_updates_at);
	}
}
