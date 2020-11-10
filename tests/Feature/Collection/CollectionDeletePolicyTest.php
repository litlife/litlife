<?php

namespace Tests\Feature\Collection;

use App\Collection;
use App\User;
use Tests\TestCase;

class CollectionDeletePolicyTest extends TestCase
{
	public function testCanIfUserCreator()
	{
		$collection = Collection::factory()->create();

		$user = $collection->create_user;

		$this->assertTrue($user->can('delete', $collection));
	}

	public function testCantIfUserNotCreator()
	{
		$collection = Collection::factory()->create();

		$user = User::factory()->create();

		$this->assertFalse($user->can('delete', $collection));
	}
}
