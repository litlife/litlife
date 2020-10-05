<?php

namespace Tests\Feature\Book\Collection;

use App\CollectedBook;
use Tests\TestCase;

class BookCollectionRelationTest extends TestCase
{
	public function test()
	{
		$collectedBook = factory(CollectedBook::class)->create();

		$book = $collectedBook->book;
		$collection = $collectedBook->collection;

		$this->assertEquals(1, $book->collections()->count());
		$this->assertTrue($collection->is($book->collections()->first()));
	}
}
