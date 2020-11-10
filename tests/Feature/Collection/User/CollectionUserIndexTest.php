<?php

namespace Tests\Feature\Collection\User;

use App\Collection;
use App\CollectionUser;
use Illuminate\Support\Str;
use Tests\TestCase;

class CollectionUserIndexTest extends TestCase
{
	public function testIndexHttp()
	{
		$collectionUser = CollectionUser::factory()->create();

		$user = $collectionUser->user;
		$collection = $collectionUser->collection;

		$this->actingAs($collection->create_user)
			->get(route('collections.users.index', $collection))
			->assertOk()
			->assertSeeText($user->nick);
	}

	public function testSeeOtherUserCollectionIfAddedToUserList()
	{
		$collection = Collection::factory()->private()->create();

		$collectionUser = CollectionUser::factory()->create(['collection_id' => $collection->id]);

		$user = $collectionUser->user;

		$this->actingAs($user)
			->get(route('collections.index', ['title' => $collection->title]))
			->assertOk()
			->assertSeeText($collection->title);
	}
}
