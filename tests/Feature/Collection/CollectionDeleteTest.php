<?php

namespace Tests\Feature\Collection;

use App\Collection;
use Tests\TestCase;

class CollectionDeleteTest extends TestCase
{
	public function testDeleteHttp()
	{
		$collection = factory(Collection::class)->create();

		$user = $collection->create_user;

		$this->actingAs($collection->create_user)
			->delete(route('collections.destroy', $collection))
			->assertOk();

		$collection->refresh();
		$user->refresh();

		$this->assertSoftDeleted($collection);
		$this->assertEquals(0, $user->data->created_collections_count);
	}

	public function testRestoreHttp()
	{
		$collection = factory(Collection::class)->create();

		$user = $collection->create_user;

		$collection->delete();

		$this->actingAs($collection->create_user)
			->delete(route('collections.destroy', $collection))
			->assertOk();

		$collection->refresh();
		$user->refresh();

		$this->assertFalse($collection->trashed());
		$this->assertEquals(1, $user->data->created_collections_count);
	}
}
