<?php

namespace Tests\Feature\Collection;

use App\Collection;
use App\User;
use Tests\TestCase;

class CollectionRestorePolicyTest extends TestCase
{
	public function testRestorePolicyIfUserCreator()
	{
		$collection = Collection::factory()->create()->fresh();

		$user = $collection->create_user;

		$collection->delete();

		$this->assertTrue($user->can('restore', $collection));
	}

	public function testRestorePolicyIfUserNotCreator()
	{
		$collection = Collection::factory()->create()->fresh();

		$user = User::factory()->create()->fresh();

		$collection->delete();

		$this->assertFalse($user->can('restore', $collection));
	}
}
