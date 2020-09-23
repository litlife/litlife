<?php

namespace Tests\Feature\Collection;

use App\Collection;
use App\User;
use Tests\TestCase;

class CollectionLikeTest extends TestCase
{
	public function testAddLike()
	{
		$collection = factory(Collection::class)
			->create();

		$user = factory(User::class)->states('administrator')->create();

		$response = $this->actingAs($user)
			->get(route('likes.store', ['type' => 18, 'id' => $collection->id]))
			->assertOk();

		$like = $collection->likes()->first();

		$collection->refresh();

		$response->assertJsonFragment($collection->toArray());
		$response->assertJsonFragment($like->toArray());

		$this->assertEquals(1, $collection->like_count);
	}
}
