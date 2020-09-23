<?php

namespace Tests\Feature\Collection;

use App\Collection;
use App\User;
use Tests\TestCase;

class CollectionFavoriteToggleTest extends TestCase
{
	public function testToggleToFavorites()
	{
		$collection = factory(Collection::class)
			->create();

		$user = factory(User::class)
			->create();

		$this->actingAs($user)
			->get(route('collections.favorite.toggle', ['collection' => $collection]))
			->assertOk()
			->assertJsonFragment(['result' => 'attached', 'count' => 1]);

		$collection->refresh();
		$user->refresh();

		$this->assertEquals(1, $collection->added_to_favorites_users_count);
		$this->assertEquals(1, $user->data->favorite_collections_count);

		$this->actingAs($user)
			->get(route('collections.favorite.toggle', ['collection' => $collection]))
			->assertOk()
			->assertJsonFragment(['result' => 'detached', 'count' => 0]);

		$collection->refresh();
		$user->refresh();

		$this->assertEquals(0, $collection->added_to_favorites_users_count);
		$this->assertEquals(0, $user->data->favorite_collections_count);
	}

	public function testUserFavoriteCollectionsHttp()
	{
		$collection = factory(Collection::class)
			->create(['title' => uniqid()]);

		$user = $collection->create_user;

		$this->actingAs($user)
			->get(route('collections.favorite.toggle', ['collection' => $collection]))
			->assertOk();

		$this->actingAs($user)
			->get(route('users.collections.favorite', ['user' => $user]))
			->assertOk()
			->assertSeeText($collection->title);
	}
}
