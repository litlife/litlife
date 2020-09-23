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
		$collectionUser = factory(CollectionUser::class)
			->create();

		$user = $collectionUser->user;
		$collection = $collectionUser->collection;

		$this->actingAs($collection->create_user)
			->get(route('collections.users.index', $collection))
			->assertOk()
			->assertSeeText($user->nick);
	}

	public function testSeeOtherUserCollectionIfAddedToUserList()
	{
		$collection = factory(Collection::class)
			->states('private')
			->create(['title' => Str::random(8)]);

		$collectionUser = factory(CollectionUser::class)
			->create(['collection_id' => $collection->id]);

		$user = $collectionUser->user;

		$this->actingAs($user)
			->get(route('collections.index', ['title' => $collection->title]))
			->assertOk()
			->assertSeeText($collection->title);
	}
}
