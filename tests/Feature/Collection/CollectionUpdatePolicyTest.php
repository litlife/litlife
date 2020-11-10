<?php

namespace Tests\Feature\Collection;

use App\Collection;
use App\CollectionUser;
use App\User;
use Tests\TestCase;

class CollectionUpdatePolicyTest extends TestCase
{
	public function testCollectionUserCanEditWithPermission()
	{
		$collectionUser = CollectionUser::factory()->create(['can_edit' => true]);

		$user = $collectionUser->user;
		$collection = $collectionUser->collection;

		$this->assertTrue($user->can('update', $collection));
	}

	public function testCollectionUserCantEditWithoutPermission()
	{
		$collectionUser = CollectionUser::factory()->create(['can_edit' => false]);

		$user = $collectionUser->user;
		$collection = $collectionUser->collection;

		$this->assertFalse($user->can('update', $collection));
	}

	public function testUpdatePolicy()
	{
		$collection = Collection::factory()->private()->create(
			)
			->fresh();

		$user = $collection->create_user;
		$user->group->manage_collections = false;
		$user->push();
		$user->refresh();

		$this->assertFalse($user->can('update', $collection));

		$user->group->manage_collections = true;
		$user->push();
		$user->refresh();

		$this->assertTrue($user->can('update', $collection));

		$other_user = User::factory()->create();
		$other_user->group->manage_collections = true;
		$other_user->push();
		$other_user->refresh();

		$this->assertFalse($other_user->can('update', $collection));
	}

	public function testEditOtherUserCollectionsUserGroup()
	{
		$collection = Collection::factory()->accepted()->create();

		$admin = User::factory()->create();
		$admin->group->edit_other_user_collections = false;
		$admin->push();

		$this->assertFalse($admin->can('update', $collection));

		$admin->group->edit_other_user_collections = true;
		$admin->push();
		$admin->refresh();

		$this->assertTrue($admin->can('update', $collection));
	}
}
