<?php

namespace Tests\Feature\User;

use App\Enums\UserRelationType;
use App\UserRelation;
use Tests\TestCase;

class UserFriendshipTest extends TestCase
{
	/**
	 * A basic test example.
	 *
	 * @return void
	 */
	public function testIfBlacklist()
	{
		$relation = factory(UserRelation::class)->create(['status' => UserRelationType::Blacklist]);

		$active_user = $relation->first_user;
		$other_user = $relation->second_user;

		$this->assertFalse($active_user->isNobodyTo($other_user));
		$this->assertFalse($active_user->isFriendOf($other_user));
		$this->assertFalse($active_user->isSubscriberOf($other_user));
		$this->assertFalse($active_user->isSubscriptionOf($other_user));
		$this->assertTrue($active_user->hasAddedToBlacklist($other_user));
		$this->assertFalse($active_user->addedToBlacklistBy($other_user));

		$this->assertFalse($other_user->isNobodyTo($active_user));
		$this->assertFalse($other_user->isFriendOf($active_user));
		$this->assertFalse($other_user->isSubscriberOf($active_user));
		$this->assertFalse($other_user->isSubscriptionOf($active_user));
		$this->assertFalse($other_user->hasAddedToBlacklist($active_user));
		$this->assertTrue($other_user->addedToBlacklistBy($active_user));
	}

	public function testIfFriend()
	{
		$relation = factory(UserRelation::class)->create(['status' => UserRelationType::Friend]);

		$active_user = $relation->first_user;
		$other_user = $relation->second_user;

		$this->assertFalse($active_user->isNobodyTo($other_user));
		$this->assertTrue($active_user->isFriendOf($other_user));
		$this->assertFalse($active_user->isSubscriberOf($other_user));
		$this->assertFalse($active_user->isSubscriptionOf($other_user));
		$this->assertFalse($active_user->hasAddedToBlacklist($other_user));
		$this->assertFalse($active_user->addedToBlacklistBy($other_user));

		$this->assertFalse($other_user->isNobodyTo($active_user));
		$this->assertTrue($other_user->isFriendOf($active_user));
		$this->assertFalse($other_user->isSubscriberOf($active_user));
		$this->assertFalse($other_user->isSubscriptionOf($active_user));
		$this->assertFalse($other_user->hasAddedToBlacklist($active_user));
		$this->assertFalse($other_user->addedToBlacklistBy($active_user));
	}

	public function testIfNobody()
	{
		$relation = factory(UserRelation::class)->create(['status' => UserRelationType::Null]);

		$active_user = $relation->first_user;
		$other_user = $relation->second_user;

		UserRelation::updateOrCreate(
			['user_id' => $other_user->id, 'user_id2' => $active_user->id],
			['status' => UserRelationType::Null, 'user_updated_at' => now()]
		);

		$this->assertTrue($active_user->isNobodyTo($other_user));
		$this->assertFalse($active_user->isFriendOf($other_user));
		$this->assertFalse($active_user->isSubscriberOf($other_user));
		$this->assertFalse($active_user->isSubscriptionOf($other_user));
		$this->assertFalse($active_user->hasAddedToBlacklist($other_user));
		$this->assertFalse($active_user->addedToBlacklistBy($other_user));

		$this->assertTrue($other_user->isNobodyTo($active_user));
		$this->assertFalse($other_user->isFriendOf($active_user));
		$this->assertFalse($other_user->isSubscriberOf($active_user));
		$this->assertFalse($other_user->isSubscriptionOf($active_user));
		$this->assertFalse($other_user->hasAddedToBlacklist($active_user));
		$this->assertFalse($other_user->addedToBlacklistBy($active_user));

		UserRelation::whereIn('user_id', [$active_user->id, $other_user->id])
			->orWhereIn('user_id2', [$active_user->id, $other_user->id])
			->delete();

		$this->assertTrue($active_user->isNobodyTo($other_user));
		$this->assertFalse($active_user->isFriendOf($other_user));
		$this->assertFalse($active_user->isSubscriberOf($other_user));
		$this->assertFalse($active_user->isSubscriptionOf($other_user));
		$this->assertFalse($active_user->hasAddedToBlacklist($other_user));
		$this->assertFalse($active_user->addedToBlacklistBy($other_user));

		$this->assertTrue($other_user->isNobodyTo($active_user));
		$this->assertFalse($other_user->isFriendOf($active_user));
		$this->assertFalse($other_user->isSubscriberOf($active_user));
		$this->assertFalse($other_user->isSubscriptionOf($active_user));
		$this->assertFalse($other_user->hasAddedToBlacklist($active_user));
		$this->assertFalse($other_user->addedToBlacklistBy($active_user));
	}

	public function testIfSubscriber()
	{
		$relation = factory(UserRelation::class)->create(['status' => UserRelationType::Subscriber]);

		$active_user = $relation->first_user;
		$other_user = $relation->second_user;

		$this->assertFalse($active_user->isNobodyTo($other_user));
		$this->assertFalse($active_user->isFriendOf($other_user));
		$this->assertTrue($active_user->isSubscriberOf($other_user));
		$this->assertFalse($active_user->isSubscriptionOf($other_user));
		$this->assertFalse($active_user->hasAddedToBlacklist($other_user));
		$this->assertFalse($active_user->addedToBlacklistBy($other_user));

		$this->assertFalse($other_user->isNobodyTo($active_user));
		$this->assertFalse($other_user->isFriendOf($active_user));
		$this->assertFalse($other_user->isSubscriberOf($active_user));
		$this->assertTrue($other_user->isSubscriptionOf($active_user));
		$this->assertFalse($other_user->hasAddedToBlacklist($active_user));
		$this->assertFalse($other_user->addedToBlacklistBy($active_user));
	}
}
