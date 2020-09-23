<?php

namespace Tests\Feature\Collection\Book;

use App\CollectionUser;
use Tests\TestCase;

class CollectionBookDetachPolicyTest extends TestCase
{
	public function testCollectionUserCanDetachBooksWithPermission()
	{
		$collectionUser = factory(CollectionUser::class)
			->create(['can_remove_books' => true]);

		$user = $collectionUser->user;
		$collection = $collectionUser->collection;

		$this->assertTrue($user->can('detachBook', $collection));
	}

	public function testCollectionUserCanDetachBooksWithoutPermission()
	{
		$collectionUser = factory(CollectionUser::class)
			->states('collection_who_can_add_me')
			->create(['can_remove_books' => false]);

		$user = $collectionUser->user;
		$collection = $collectionUser->collection;

		$this->assertFalse($user->can('detachBook', $collection));
	}
}
