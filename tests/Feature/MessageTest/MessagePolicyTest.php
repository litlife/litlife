<?php

namespace Tests\Feature\MessageTest;

use App\Enums\UserAccountPermissionValues;
use App\Enums\UserRelationType;
use App\Message;
use App\User;
use App\UserRelation;
use Tests\TestCase;

class MessagePolicyTest extends TestCase
{
	public function testCreatePolicyIfUserInBlacklist()
	{
		// if blacklist

		$relation = factory(UserRelation::class)
			->create([
				'status' => UserRelationType::Blacklist
			]);

		$first_user = $relation->first_user;
		$second_user = $relation->second_user;

		$this->assertTrue($first_user->hasAddedToBlacklist($second_user));

		$second_user->account_permissions->write_private_messages = UserAccountPermissionValues::everyone;
		$second_user->account_permissions->save();

		$this->assertFalse($first_user->can('write_private_messages', $second_user));
		$this->assertFalse($second_user->can('write_private_messages', $first_user));

		$second_user->account_permissions->write_private_messages = UserAccountPermissionValues::friends;
		$second_user->account_permissions->save();

		$this->assertFalse($first_user->can('write_private_messages', $second_user));
		$this->assertFalse($second_user->can('write_private_messages', $first_user));

		$second_user->account_permissions->write_private_messages = UserAccountPermissionValues::friends_and_subscribers;
		$second_user->account_permissions->save();

		$this->assertFalse($first_user->can('write_private_messages', $second_user));
		$this->assertFalse($second_user->can('write_private_messages', $first_user));

		$second_user->account_permissions->write_private_messages = UserAccountPermissionValues::friends_and_subscriptions;
		$second_user->account_permissions->save();

		$this->assertFalse($first_user->can('write_private_messages', $second_user));
		$this->assertFalse($second_user->can('write_private_messages', $first_user));
	}

	public function testCreatePolicyIfUserIsNobody()
	{
		$relation = factory(UserRelation::class)
			->create(['status' => UserRelationType::Null]);

		$first_user = $relation->first_user;
		$second_user = $relation->second_user;

		$this->assertTrue($first_user->isNobodyTo($second_user));
		$this->assertTrue($second_user->isNobodyTo($first_user));

		$second_user->account_permissions->write_private_messages = UserAccountPermissionValues::everyone;
		$second_user->account_permissions->save();

		$this->assertTrue($first_user->can('write_private_messages', $second_user));

		$second_user->account_permissions->write_private_messages = UserAccountPermissionValues::friends_and_subscribers;
		$second_user->account_permissions->save();

		$this->assertFalse($first_user->can('write_private_messages', $second_user));

		$second_user->account_permissions->write_private_messages = UserAccountPermissionValues::friends;
		$second_user->account_permissions->save();

		$this->assertFalse($first_user->can('write_private_messages', $second_user));

		$second_user->account_permissions->write_private_messages = UserAccountPermissionValues::friends_and_subscriptions;
		$second_user->account_permissions->save();

		$this->assertFalse($first_user->can('write_private_messages', $second_user));
	}

	public function testCreateIfUserIsSubscriber()
	{
		$relation = factory(UserRelation::class)
			->create(['status' => UserRelationType::Subscriber]);

		$first_user = $relation->first_user;
		$second_user = $relation->second_user;

		$this->assertTrue($first_user->isSubscriberOf($second_user));
		$this->assertTrue($second_user->isSubscriptionOf($first_user));

		$second_user->account_permissions->write_private_messages = UserAccountPermissionValues::everyone;
		$second_user->account_permissions->save();

		$this->assertTrue($first_user->can('write_private_messages', $second_user));

		$second_user->account_permissions->write_private_messages = UserAccountPermissionValues::friends_and_subscribers;
		$second_user->account_permissions->save();

		$this->assertTrue($first_user->can('write_private_messages', $second_user));

		$second_user->account_permissions->write_private_messages = UserAccountPermissionValues::friends;
		$second_user->account_permissions->save();

		$this->assertFalse($first_user->can('write_private_messages', $second_user));

		$second_user->account_permissions->write_private_messages = UserAccountPermissionValues::friends_and_subscriptions;
		$second_user->account_permissions->save();

		$this->assertFalse($first_user->can('write_private_messages', $second_user));
	}

	public function testCreateIfUserIsSubscription()
	{
		$relation = factory(UserRelation::class)
			->create(['status' => UserRelationType::Subscriber]);

		$me = $relation->first_user;
		$user = $relation->second_user;

		$this->assertTrue($me->isSubscriberOf($user));

		$me->account_permissions->write_private_messages = UserAccountPermissionValues::everyone;
		$me->account_permissions->save();

		$this->assertTrue($user->can('write_private_messages', $me));

		$me->account_permissions->write_private_messages = UserAccountPermissionValues::friends_and_subscribers;
		$me->account_permissions->save();

		$this->assertFalse($user->can('write_private_messages', $me));

		$me->account_permissions->write_private_messages = UserAccountPermissionValues::friends;
		$me->account_permissions->save();

		$this->assertFalse($user->can('write_private_messages', $me));

		$me->account_permissions->write_private_messages = UserAccountPermissionValues::friends_and_subscriptions;
		$me->account_permissions->save();

		$this->assertTrue($user->can('write_private_messages', $me));
	}

	public function testCreateIfUserIsFriend()
	{
		$relation = factory(UserRelation::class)->create(['status' => UserRelationType::Friend]);

		$first_user = $relation->first_user;
		$second_user = $relation->second_user;

		$this->assertTrue($first_user->isFriendOf($second_user));

		$this->assertFalse($first_user->fresh()->can('write_private_messages', $first_user));

		$second_user->account_permissions->write_private_messages = UserAccountPermissionValues::everyone;
		$second_user->account_permissions->save();

		$this->assertTrue($first_user->can('write_private_messages', $second_user));

		$second_user->account_permissions->write_private_messages = UserAccountPermissionValues::friends;
		$second_user->account_permissions->save();

		$this->assertTrue($first_user->can('write_private_messages', $second_user));

		$second_user->account_permissions->write_private_messages = UserAccountPermissionValues::friends_and_subscribers;
		$second_user->account_permissions->save();

		$this->assertTrue($first_user->can('write_private_messages', $second_user));

		$second_user->account_permissions->write_private_messages = UserAccountPermissionValues::friends_and_subscriptions;
		$second_user->account_permissions->save();

		$this->assertTrue($first_user->can('write_private_messages', $second_user));
		$this->assertTrue($second_user->can('write_private_messages', $first_user));
	}

	public function testCreatePolicyIfAcessSendPrivateMessagesAvoidEnable()
	{
		$admin = factory(User::class)->create();

		$user = factory(User::class)->create();
		$user->account_permissions->write_private_messages = UserAccountPermissionValues::friends;
		$user->account_permissions->save();

		$this->assertFalse($admin->fresh()->can('write_private_messages', $user->fresh()));

		$relation = factory(UserRelation::class)
			->create([
				'status' => UserRelationType::Blacklist,
				'user_id' => $user->id,
				'user_id2' => $admin->id
			]);

		$this->assertTrue($user->fresh()->hasAddedToBlacklist($admin->fresh()));

		$this->assertFalse($admin->fresh()->can('write_private_messages', $user->fresh()));

		$admin->group->access_send_private_messages_avoid_privacy_and_blacklists = true;
		$admin->push();

		$this->assertTrue($admin->fresh()->can('write_private_messages', $user->fresh()));
	}

	public function testDeleteRestorePolicy()
	{
		$recepient = factory(User::class)->create()->fresh();

		$other_user = factory(User::class)->create()->fresh();

		$message = factory(Message::class)
			->states('viewed')
			->create([
				'recepient_id' => $recepient->id
			])
			->fresh();

		$this->assertTrue($recepient->can('delete', $message));
		$this->assertFalse($recepient->can('restore', $message));

		$this->assertTrue($message->create_user->can('delete', $message));
		$this->assertFalse($message->create_user->can('restore', $message));

		$this->assertFalse($other_user->can('delete', $message));
		$this->assertFalse($other_user->can('restore', $message));

		$message->deleteForUser($recepient);

		$message = Message::joinUserDeletions($recepient->id)->findOrFail($message->id);
		$this->assertTrue($recepient->can('restore', $message));
		$this->assertFalse($recepient->can('delete', $message));

		$message = Message::joinUserDeletions($message->create_user->id)->findOrFail($message->id);
		$this->assertTrue($message->create_user->can('delete', $message));
		$this->assertFalse($message->create_user->can('restore', $message));

		$this->assertFalse($other_user->can('delete', $message));
		$this->assertFalse($other_user->can('restore', $message));
	}
}
