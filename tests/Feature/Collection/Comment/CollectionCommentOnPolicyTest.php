<?php

namespace Tests\Feature\Collection\Comment;

use App\Collection;
use App\CollectionUser;
use App\Enums\UserRelationType;
use App\User;
use App\UserRelation;
use Tests\TestCase;

class CollectionCommentOnPolicyTest extends TestCase
{
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
		//$this->assertFalse($friend->can('commentOn', $collection));
		//$this->assertFalse($subscriber->can('commentOn', $collection));
		$this->assertFalse($nobody_user->can('commentOn', $collection));
		/*
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
		*/
		$collection->who_can_comment = 'everyone';
		$collection->save();
		$collection->refresh();

		$this->assertTrue($creator->can('commentOn', $collection));
		//$this->assertTrue($friend->can('commentOn', $collection));
		//$this->assertTrue($subscriber->can('commentOn', $collection));
		$this->assertTrue($nobody_user->can('commentOn', $collection));
	}
}
