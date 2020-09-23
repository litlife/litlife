<?php

namespace Tests\Feature\Collection;

use App\Collection;
use App\Enums\StatusEnum;
use App\User;
use Tests\TestCase;

class CollectionScopeTest extends TestCase
{
	public function testScopeSeeEveryone()
	{
		$collection = factory(Collection::class)
			->states('accepted')
			->create()
			->fresh();

		$this->assertEquals(1, Collection::where('id', $collection->id)
			->seeEveryone()
			->count());

		$collection->status = StatusEnum::Private;
		$collection->save();
		$collection->refresh();

		$this->assertEquals(0, Collection::where('id', $collection->id)
			->seeEveryone()
			->count());
	}

	public function testUserSeesScopeOnlyMe()
	{
		$collection = factory(Collection::class)
			->states('private')
			->create()
			->fresh();

		$creator = $collection->create_user;

		$this->assertEquals(1, Collection::where('id', $collection->id)
			->userSees($creator)
			->count());

		$user = factory(User::class)->create();

		$this->assertEquals(0, Collection::where('id', $collection->id)
			->userSees($user)
			->count());
	}

	public function testUserSeesScopeEveryone()
	{
		$collection = factory(Collection::class)
			->states('accepted')
			->create()
			->fresh();

		$creator = $collection->create_user;

		$this->assertEquals(1, Collection::where('id', $collection->id)
			->userSees($creator)
			->count());

		$user = factory(User::class)->create();

		$this->assertEquals(1, Collection::where('id', $collection->id)
			->userSees($user)
			->count());
	}

	/*
	public function testScopeUserSeesFriend()
	{
		$collection = factory(Collection::class)
			->create(['who_can_see' => 'friends'])
			->fresh();

		$creator = $collection->create_user;

		$this->assertEquals(1, Collection::where('id', $collection->id)
			->userSees($creator)
			->count());

		$user = factory(User::class)->create();

		$this->assertEquals(0, Collection::where('id', $collection->id)
			->userSees($user)
			->count());

		$relation = factory(UserRelation::class)
			->create([
				'user_id' => $creator->id,
				'status' => \App\Enums\UserRelationType::Friend
			]);

		$user = $relation->second_user;

		$this->assertTrue($creator->isFriendOf($user));

		$this->assertEquals(1, Collection::where('id', $collection->id)
			->userSees($user)
			->count());

		$relation = factory(UserRelation::class)
			->create([
				'user_id2' => $creator->id,
				'status' => \App\Enums\UserRelationType::Subscriber
			]);

		$user = $relation->first_user;

		$this->assertTrue($user->isSubscriberOf($creator));

		$this->assertEquals(0, Collection::where('id', $collection->id)
			->userSees($user)
			->count());
	}
	*/
	/*
		public function testScopeUserSeesSubscriberAndFriends()
		{
			$collection = factory(Collection::class)
				->create(['who_can_see' => 'friends_and_subscribers'])
				->fresh();

			$creator = $collection->create_user;

			$this->assertEquals(1, Collection::where('id', $collection->id)
				->userSees($creator)
				->count());

			$user = factory(User::class)->create();

			$this->assertEquals(0, Collection::where('id', $collection->id)
				->userSees($user)
				->count());

			$relation = factory(UserRelation::class)
				->create([
					'user_id' => $creator->id,
					'status' => \App\Enums\UserRelationType::Friend
				]);

			$user = $relation->second_user;

			$this->assertTrue($creator->isFriendOf($user));

			$this->assertEquals(1, Collection::where('id', $collection->id)
				->userSees($user)
				->count());

			$relation = factory(UserRelation::class)
				->create([
					'user_id2' => $creator->id,
					'status' => \App\Enums\UserRelationType::Subscriber
				]);

			$user = $relation->first_user;

			$this->assertTrue($user->isSubscriberOf($creator));
			$this->assertTrue($creator->isSubscriptionOf($user));

			$this->assertEquals(1, Collection::where('id', $collection->id)
				->userSees($user)
				->count());
		}
	*/
}
