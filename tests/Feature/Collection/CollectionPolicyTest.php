<?php

namespace Tests\Feature\Collection;

use App\Collection;
use App\CollectionUser;
use App\Enums\StatusEnum;
use App\Enums\UserAccountPermissionValues;
use App\Enums\UserRelationType;
use App\User;
use App\UserRelation;
use Tests\TestCase;

class CollectionPolicyTest extends TestCase
{
	public function testCollectionCreatorCanCreateUser()
	{
		$collection = factory(Collection::class)
			->create();

		$this->assertTrue($collection->create_user->can('createUser', $collection));
	}

	public function testCollectionCreatorCanEditUser()
	{
		$collection = factory(Collection::class)
			->create();

		$this->assertTrue($collection->create_user->can('editUser', $collection));
	}

	public function testCollectionCreatorCanDeleteUser()
	{
		$collection = factory(Collection::class)
			->create();

		$this->assertTrue($collection->create_user->can('deleteUser', $collection));
	}

	public function testOtherUserCantCreateUser()
	{
		$collection = factory(Collection::class)
			->create();

		$user = factory(User::class)
			->create();

		$this->assertFalse($user->can('createUser', $collection));
	}

	public function testOtherUserCantEditUser()
	{
		$collection = factory(Collection::class)
			->create();

		$user = factory(User::class)
			->create();

		$this->assertFalse($user->can('editUser', $collection));
	}

	public function testOtherUserCantDeleteUser()
	{
		$collection = factory(Collection::class)
			->create();

		$user = factory(User::class)
			->create();

		$this->assertFalse($user->can('deleteUser', $collection));
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

	public function testCollectionUserCanEditWithPermission()
	{
		$collectionUser = factory(CollectionUser::class)
			->create(['can_edit' => true]);

		$user = $collectionUser->user;
		$collection = $collectionUser->collection;

		$this->assertTrue($user->can('update', $collection));
	}

	public function testCollectionUserCantEditWithoutPermission()
	{
		$collectionUser = factory(CollectionUser::class)
			->create(['can_edit' => false]);

		$user = $collectionUser->user;
		$collection = $collectionUser->collection;

		$this->assertFalse($user->can('update', $collection));
	}

	public function testCollectionUserCanAddBooksWithPermission()
	{
		$collectionUser = factory(CollectionUser::class)
			->create(['can_add_books' => true]);

		$user = $collectionUser->user;
		$collection = $collectionUser->collection;

		$this->assertTrue($user->can('addBook', $collection));
	}

	public function testCollectionUserCanAddBooksWithoutPermission()
	{
		$collectionUser = factory(CollectionUser::class)
			->states('collection_who_can_add_me')
			->create(['can_add_books' => false]);

		$user = $collectionUser->user;
		$collection = $collectionUser->collection;

		$this->assertFalse($user->can('addBook', $collection));
	}

	public function testCollectionUserCanDetachBooksWithPermission()
	{
		$collectionUser = factory(CollectionUser::class)
			->create(['can_remove_books' => true]);

		$user = $collectionUser->user;
		$collection = $collectionUser->collection;

		$this->assertTrue($user->can('detachBook', $collection));
	}

	public function testCollectionUserCanDetachBooksWithoutPermission()
	{
		$collectionUser = factory(CollectionUser::class)
			->states('collection_who_can_add_me')
			->create(['can_remove_books' => false]);

		$user = $collectionUser->user;
		$collection = $collectionUser->collection;

		$this->assertFalse($user->can('detachBook', $collection));
	}

	public function testCollectionUserCanEditBookDescriptionWithPermission()
	{
		$collectionUser = factory(CollectionUser::class)
			->create(['can_edit_books_description' => true]);

		$user = $collectionUser->user;
		$collection = $collectionUser->collection;

		$this->assertTrue($user->can('editBookDescription', $collection));
	}

	public function testCollectionUserCanEditBookDescriptionWithoutPermission()
	{
		$collectionUser = factory(CollectionUser::class)
			->states('collection_who_can_add_me')
			->create(['can_edit_books_description' => false]);

		$user = $collectionUser->user;
		$collection = $collectionUser->collection;

		$this->assertFalse($user->can('editBookDescription', $collection));
	}

	public function testCollectionUserCanCommentWithPermission()
	{
		$collectionUser = factory(CollectionUser::class)
			->create(['can_comment' => true]);

		$user = $collectionUser->user;
		$user->group->add_comment = true;
		$user->push();

		$collection = $collectionUser->collection;

		$this->assertTrue($user->can('commentOn', $collection));
	}

	public function testCollectionUserCanCommentWithoutPermission()
	{
		$collectionUser = factory(CollectionUser::class)
			->create(['can_comment' => false]);

		$user = $collectionUser->user;
		$collection = $collectionUser->collection;

		$this->assertFalse($user->can('commentOn', $collection));
	}

	public function testCollectionUserCantCommentWithoutGlobalPermission()
	{
		$collectionUser = factory(CollectionUser::class)
			->create(['can_comment' => true]);

		$user = $collectionUser->user;
		$user->group->add_comment = false;
		$user->push();

		$collection = $collectionUser->collection;

		$this->assertFalse($user->can('commentOn', $collection));
	}

	public function testCollectionCreatorCanCreateUserIfCollectionPrivate()
	{
		$collection = factory(Collection::class)
			->states('private')
			->create();

		$this->assertTrue($collection->create_user->can('createUser', $collection));
	}

	public function testCollectionUserCanEditUserWithPermission()
	{
		$collectionUser = factory(CollectionUser::class)
			->create(['can_user_manage' => true]);

		$user = $collectionUser->user;
		$collection = $collectionUser->collection;

		$this->assertTrue($user->can('editUser', $collection));
	}

	public function testCollectionUserCantEditUserWithoutPermission()
	{
		$collectionUser = factory(CollectionUser::class)
			->create(['can_user_manage' => false]);

		$user = $collectionUser->user;
		$collection = $collectionUser->collection;

		$this->assertFalse($user->can('editUser', $collection));
	}

	public function testCollectionUserCanDeleteUserWithPermission()
	{
		$collectionUser = factory(CollectionUser::class)
			->create(['can_user_manage' => true]);

		$user = $collectionUser->user;
		$collection = $collectionUser->collection;

		$this->assertTrue($user->can('deleteUser', $collection));
	}

	public function testCollectionUserCantDeleteUserWithoutPermission()
	{
		$collectionUser = factory(CollectionUser::class)
			->create(['can_user_manage' => false]);

		$user = $collectionUser->user;
		$collection = $collectionUser->collection;

		$this->assertFalse($user->can('deleteUser', $collection));
	}

	public function testAddBookPolicy()
	{
		$collection = factory(Collection::class)
			->create(['who_can_add' => 'me'])
			->fresh();

		$creator = $collection->create_user;

		$relation = factory(UserRelation::class)
			->create([
				'user_id' => $creator->id,
				'status' => UserRelationType::Friend
			]);

		$friend = $relation->second_user;

		$relation = factory(UserRelation::class)
			->create([
				'user_id2' => $creator->id,
				'status' => UserRelationType::Subscriber
			]);

		$subscriber = $relation->first_user;

		$nobody_user = factory(User::class)->create();

		$this->assertTrue($creator->can('addBook', $collection));
		$this->assertFalse($friend->can('addBook', $collection));
		$this->assertFalse($subscriber->can('addBook', $collection));
		$this->assertFalse($nobody_user->can('addBook', $collection));

		$collection->who_can_add = 'friends';
		$collection->save();
		$collection->refresh();

		$this->assertEquals(UserAccountPermissionValues::friends, $collection->who_can_add);

		$this->assertTrue($creator->can('addBook', $collection));
		$this->assertTrue($friend->can('addBook', $collection));
		$this->assertFalse($subscriber->can('addBook', $collection));
		$this->assertFalse($nobody_user->can('addBook', $collection));

		$collection->who_can_add = 'friends_and_subscribers';
		$collection->save();
		$collection->refresh();

		$this->assertTrue($creator->can('addBook', $collection));
		$this->assertTrue($friend->can('addBook', $collection));
		$this->assertTrue($subscriber->can('addBook', $collection));
		$this->assertFalse($nobody_user->can('addBook', $collection));

		$collection->who_can_add = 'everyone';
		$collection->save();
		$collection->refresh();

		$this->assertTrue($creator->can('addBook', $collection));
		$this->assertTrue($friend->can('addBook', $collection));
		$this->assertTrue($subscriber->can('addBook', $collection));
		$this->assertTrue($nobody_user->can('addBook', $collection));
	}

	public function testAddBookPolicyManageCollections()
	{
		$collection = factory(Collection::class)
			->create(['who_can_add' => 'me'])
			->fresh();

		$user = $collection->create_user;

		$this->assertTrue($user->can('addBook', $collection));

		$user->group->manage_collections = false;
		$user->push();
		$user->refresh();

		$this->assertFalse($user->can('addBook', $collection));
	}

	public function testViewPolicy()
	{
		$collection = factory(Collection::class)
			->states('private')
			->create()
			->fresh();

		$creator = $collection->create_user;

		$relation = factory(UserRelation::class)
			->create([
				'user_id' => $creator->id,
				'status' => UserRelationType::Friend
			]);

		$friend = $relation->second_user;

		$relation = factory(UserRelation::class)
			->create([
				'user_id2' => $creator->id,
				'status' => UserRelationType::Subscriber
			]);

		$subscriber = $relation->first_user;

		$nobody_user = factory(User::class)->create();

		$this->assertTrue($creator->can('view', $collection));
		$this->assertFalse($friend->can('view', $collection));
		$this->assertFalse($subscriber->can('view', $collection));
		$this->assertFalse($nobody_user->can('view', $collection));

		$collection->status = StatusEnum::Private;
		$collection->save();
		$collection->refresh();

		$this->assertEquals(StatusEnum::Private, $collection->status);

		$collection->status = StatusEnum::Accepted;
		$collection->save();
		$collection->refresh();

		$this->assertTrue($creator->can('view', $collection));
		$this->assertTrue($friend->can('view', $collection));
		$this->assertTrue($subscriber->can('view', $collection));
		$this->assertTrue($nobody_user->can('view', $collection));
	}

	public function testCreatePolicy()
	{
		$user = factory(User::class)->create();
		$user->group->manage_collections = true;
		$user->push();
		$user->refresh();

		$this->assertTrue($user->can('create', Collection::class));

		$user->group->manage_collections = false;
		$user->push();
		$user->refresh();

		$this->assertFalse($user->can('create', Collection::class));
	}

	public function testUpdatePolicy()
	{
		$collection = factory(Collection::class)
			->states('private')
			->create()
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

		$other_user = factory(User::class)->create();
		$other_user->group->manage_collections = true;
		$other_user->push();
		$other_user->refresh();

		$this->assertFalse($other_user->can('update', $collection));
	}

	public function testDeletePolicyIfUserCreator()
	{
		$collection = factory(Collection::class)->create()->fresh();

		$user = $collection->create_user;

		$this->assertTrue($user->can('delete', $collection));
	}

	public function testDeletePolicyIfUserNotCreator()
	{
		$collection = factory(Collection::class)->create()->fresh();

		$user = factory(User::class)->create()->fresh();

		$this->assertFalse($user->can('delete', $collection));
	}

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

	public function testCommentOnPolicyIfHavePermission()
	{
		$collection = factory(Collection::class)->create()->fresh();

		$user = $collection->create_user;
		$user->group->add_comment = true;
		$user->push();

		$this->assertTrue($user->can('commentOn', $collection));
	}

	public function testCommentOnPolicyIfDoesNotHavePermission()
	{
		$collection = factory(Collection::class)->create()->fresh();

		$user = $collection->create_user;
		$user->group->add_comment = false;
		$user->push();

		$this->assertFalse($user->can('commentOn', $collection));
	}

	public function testCommentOnPolicy()
	{
		$collection = factory(Collection::class)
			->create(['who_can_comment' => 'me'])
			->fresh();

		$creator = $collection->create_user;
		$creator->group->add_comment = true;
		$creator->push();

		$relation = factory(UserRelation::class)
			->create([
				'user_id' => $creator->id,
				'status' => UserRelationType::Friend
			]);

		$friend = $relation->second_user;
		$friend->group->add_comment = true;
		$friend->push();

		$relation = factory(UserRelation::class)
			->create([
				'user_id2' => $creator->id,
				'status' => UserRelationType::Subscriber
			]);

		$subscriber = $relation->first_user;
		$subscriber->group->add_comment = true;
		$subscriber->push();

		$nobody_user = factory(User::class)->create();
		$nobody_user->group->add_comment = true;
		$nobody_user->push();

		$this->assertTrue($creator->can('commentOn', $collection));
		$this->assertFalse($friend->can('commentOn', $collection));
		$this->assertFalse($subscriber->can('commentOn', $collection));
		$this->assertFalse($nobody_user->can('commentOn', $collection));

		$collection->who_can_comment = 'friends';
		$collection->save();
		$collection->refresh();

		$this->assertEquals(UserAccountPermissionValues::friends, $collection->who_can_comment);

		$this->assertTrue($creator->can('commentOn', $collection));
		$this->assertTrue($friend->can('commentOn', $collection));
		$this->assertFalse($subscriber->can('commentOn', $collection));
		$this->assertFalse($nobody_user->can('commentOn', $collection));

		$collection->who_can_comment = 'friends_and_subscribers';
		$collection->save();
		$collection->refresh();

		$this->assertTrue($creator->can('commentOn', $collection));
		$this->assertTrue($friend->can('commentOn', $collection));
		$this->assertTrue($subscriber->can('commentOn', $collection));
		$this->assertFalse($nobody_user->can('commentOn', $collection));

		$collection->who_can_comment = 'everyone';
		$collection->save();
		$collection->refresh();

		$this->assertTrue($creator->can('commentOn', $collection));
		$this->assertTrue($friend->can('commentOn', $collection));
		$this->assertTrue($subscriber->can('commentOn', $collection));
		$this->assertTrue($nobody_user->can('commentOn', $collection));
	}

	public function testEditOtherUserCollectionsUserGroup()
	{
		$collection = factory(Collection::class)
			->states('accepted')
			->create();

		$admin = factory(User::class)
			->create();
		$admin->group->edit_other_user_collections = false;
		$admin->push();

		$this->assertFalse($admin->can('update', $collection));

		$admin->group->edit_other_user_collections = true;
		$admin->push();
		$admin->refresh();

		$this->assertTrue($admin->can('update', $collection));
	}

	public function testCantViewIfNotCollectionUser()
	{
		$collection = factory(Collection::class)
			->states('private')
			->create();

		$user = factory(User::class)->create();

		$this->assertFalse($user->can('view', $collection));
	}

	public function testCanViewIfCollectionUser()
	{
		$collection = factory(Collection::class)
			->states('private')
			->create();

		$user = factory(User::class)->create();

		$collectionUser = factory(CollectionUser::class)
			->create([
				'collection_id' => $collection->id,
				'user_id' => $user->id
			]);

		$user->refresh();

		$this->assertTrue($user->can('view', $collection));
	}

	public function testCreateUserIfCollectionPrivate()
	{
		$collection = factory(Collection::class)
			->states('private')
			->create();

		$this->assertTrue($collection->create_user->can('createUser', $collection));
	}
}
