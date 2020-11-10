<?php

namespace Tests\Feature\Collection\User;

use App\Collection;
use App\CollectionUser;
use App\User;
use Tests\TestCase;

class CollectionUserCreatePolicyTest extends TestCase
{
	public function testCollectionCreatorCanCreateUser()
	{
		$collection = Collection::factory()->create();

		$this->assertTrue($collection->create_user->can('createUser', $collection));
	}

	public function testOtherUserCantCreateUser()
	{
		$collection = Collection::factory()->create();

		$user = User::factory()->create();

		$this->assertFalse($user->can('createUser', $collection));
	}

	public function testCollectionUserCanAddUserWithPermission()
	{
		$collectionUser = CollectionUser::factory()->create(['can_user_manage' => true]);

		$user = $collectionUser->user;
		$collection = $collectionUser->collection;

		$this->assertTrue($user->can('createUser', $collection));
	}

	public function testCollectionUserCantAddUserWithoutPermission()
	{
		$collectionUser = CollectionUser::factory()->create(['can_user_manage' => false]);

		$user = $collectionUser->user;
		$collection = $collectionUser->collection;

		$this->assertFalse($user->can('createUser', $collection));
	}

	public function testCollectionCreatorCanCreateUserIfCollectionPrivate()
	{
		$collection = Collection::factory()->private()->create();

		$this->assertTrue($collection->create_user->can('createUser', $collection));
	}

	public function testCreateUserIfCollectionPrivate()
	{
		$collection = Collection::factory()->private()->create();

		$this->assertTrue($collection->create_user->can('createUser', $collection));
	}
}
