<?php

namespace Tests\Feature\Collection;

use App\Collection;
use App\User;
use Tests\TestCase;

class CollectionRestorePolicyTest extends TestCase
{
	public function testRestorePolicyIfUserCreator()
	{
		$collection = factory(Collection::class)->create()->fresh();

		$user = $collection->create_user;

		$collection->delete();

		$this->assertTrue($user->can('restore', $collection));
	}

	public function testRestorePolicyIfUserNotCreator()
	{
		$collection = factory(Collection::class)->create()->fresh();

		$user = factory(User::class)->create()->fresh();

		$collection->delete();

		$this->assertFalse($user->can('restore', $collection));
	}
}
