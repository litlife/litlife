<?php

namespace Tests\Feature\Collection\Book;

use App\CollectionUser;
use Tests\TestCase;

class CollectionBookDetachPolicyTest extends TestCase
{
	public function testCollectionUserCanDetachBooksWithPermission()
	{
		$collectionUser = CollectionUser::factory()->create(['can_remove_books' => true]);

		$user = $collectionUser->user;
		$collection = $collectionUser->collection;

		$this->assertTrue($user->can('detachBook', $collection));
	}

	public function testCollectionUserCanDetachBooksWithoutPermission()
	{
		$collectionUser = CollectionUser::factory()->collection_who_can_add_me()->create();

		$user = $collectionUser->user;
		$collection = $collectionUser->collection;

		$this->assertFalse($user->can('detachBook', $collection));
	}
}
