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
		$collection = factory(Collection::class)
			->create();

		$this->assertTrue($collection->create_user->can('createUser', $collection));
	}

	public function testOtherUserCantCreateUser()
	{
		$collection = factory(Collection::class)
			->create();

		$user = factory(User::class)
			->create();

		$this->assertFalse($user->can('createUser', $collection));
	}

	public function testCollectionUserCanAddUserWithPermission()
	{
		$collectionUser = factory(CollectionUser::class)
			->create(['can_user_manage' => true]);

		$user = $collectionUser->user;
		$collection = $collectionUser->collection;

		$this->assertTrue($user->can('createUser', $collection));
	}

	public function testCollectionUserCantAddUserWithoutPermission()
	{
		$collectionUser = factory(CollectionUser::class)
			->create(['can_user_manage' => false]);

		$user = $collectionUser->user;
		$collection = $collectionUser->collection;

		$this->assertFalse($user->can('createUser', $collection));
	}

	public function testCollectionCreatorCanCreateUserIfCollectionPrivate()
	{
		$collection = factory(Collection::class)
			->states('private')
			->create();

		$this->assertTrue($collection->create_user->can('createUser', $collection));
	}

	public function testCreateUserIfCollectionPrivate()
	{
		$collection = factory(Collection::class)
			->states('private')
			->create();

		$this->assertTrue($collection->create_user->can('createUser', $collection));
	}
}
