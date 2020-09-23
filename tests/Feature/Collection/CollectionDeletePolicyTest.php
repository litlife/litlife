<?php

namespace Tests\Feature\Collection;

use App\Collection;
use App\User;
use Tests\TestCase;

class CollectionDeletePolicyTest extends TestCase
{
	public function testCanIfUserCreator()
	{
		$collection = factory(Collection::class)->create();

		$user = $collection->create_user;

		$this->assertTrue($user->can('delete', $collection));
	}

	public function testCantIfUserNotCreator()
	{
		$collection = factory(Collection::class)->create();

		$user = factory(User::class)->create();

		$this->assertFalse($user->can('delete', $collection));
	}
}
