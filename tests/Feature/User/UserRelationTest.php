<?php

namespace Tests\Feature\User;

use App\Enums\UserRelationType;
use App\Notifications\NewSubscriberNotification;
use App\User;
use App\UserRelation;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class UserRelationTest extends TestCase
{
	/**
	 * A basic test example.
	 *
	 * @return void
	 */
	public function testIndexHttp()
	{
		$user = User::factory()->create()->fresh();

		$this->actingAs($user)
			->get(route('users'))
			->assertOk();
	}

	public function testBlock()
	{
		$auth_user = User::factory()->create();

		$user = User::factory()->create();

		$this->actingAs($auth_user)
			->get(route('users.block', compact('user')))
			->assertRedirect(route('profile', compact('user')));

		$auth_user->refresh();

		$this->assertTrue($auth_user->hasAddedToBlacklist($user));
		$this->assertEquals($user->id, $auth_user->blacklists->first()->id);

		$this->actingAs($auth_user)
			->get(route('users.blacklists', ['user' => $auth_user]))
			->assertOk()
			->assertSeeText($user->userName);
	}

	public function testUnblock()
	{
		$auth_user = User::factory()->create();

		$user = User::factory()->create();

		$user_relation = UserRelation::factory()->create([
				'user_id' => $auth_user->id,
				'user_id2' => $user->id,
				'status' => UserRelationType::Blacklist
			]);

		$auth_user->refresh();
		$user->refresh();

		$this->assertTrue($auth_user->hasAddedToBlacklist($user));
		$this->assertEquals($user->id, $auth_user->blacklists->first()->id);

		$this->actingAs($auth_user)
			->get(route('users.unblock', compact('user')))
			->assertRedirect(route('profile', compact('user')));

		$auth_user->refresh();
		$user->refresh();

		$this->assertFalse($auth_user->hasAddedToBlacklist($user));
		$this->assertNull($auth_user->blacklists->first());
		$this->assertTrue($auth_user->isNobodyTo($user));
		$this->assertTrue($user->isNobodyTo($auth_user));

		$this->actingAs($auth_user)
			->get(route('users.blacklists', ['user' => $auth_user]))
			->assertOk()
			->assertDontSeeText($user->userName);
	}

	public function testRestoreFriendshipIfSavingHttp()
	{
		$auth_user = User::factory()->create();

		$user = User::factory()->create();

		$user_relation = UserRelation::factory()->create([
				'user_id' => $auth_user->id,
				'user_id2' => $user->id,
				'status' => UserRelationType::Subscriber
			]);

		$user_relation2 = UserRelation::factory()->create([
				'user_id' => $user->id,
				'user_id2' => $auth_user->id,
				'status' => UserRelationType::Subscriber
			]);

		UserRelation::where('user_id', $auth_user->id)
			->where('user_id2', $user->id)
			->update([
				'status' => UserRelationType::Subscriber
			]);

		UserRelation::where('user_id', $user->id)
			->where('user_id2', $auth_user->id)
			->update([
				'status' => UserRelationType::Subscriber
			]);

		$user_relation = UserRelation::where('user_id', $auth_user->id)
			->where('user_id2', $user->id)
			->first();

		$user_relation2 = UserRelation::where('user_id', $user->id)
			->where('user_id2', $auth_user->id)
			->first();

		$this->assertEquals(UserRelationType::Subscriber, $user_relation->status);
		$this->assertEquals(UserRelationType::Subscriber, $user_relation2->status);

		$user_relation->save();
		$user_relation2->save();

		$user_relation = UserRelation::where('user_id', $auth_user->id)
			->where('user_id2', $user->id)
			->first();

		$user_relation2 = UserRelation::where('user_id', $user->id)
			->where('user_id2', $auth_user->id)
			->first();

		$this->assertEquals(UserRelationType::Friend, $user_relation->status);
		$this->assertEquals(UserRelationType::Friend, $user_relation2->status);
	}

	public function testSubscribeHttp()
	{
		Notification::fake();

		$auth_user = User::factory()->create();

		$user = User::factory()->create();

		$response = $this->actingAs($auth_user)
			->get(route('users.subscribe', $user));
		//dump(session('errors'));
		$response->assertSessionHasNoErrors()
			->assertRedirect();

		$auth_user->refresh();
		$user->refresh();

		$this->assertTrue($auth_user->isSubscriberOf($user));
		$this->assertFalse($user->isSubscriberOf($auth_user));
		$this->assertFalse($auth_user->isFriendOf($user));
		$this->assertFalse($user->isFriendOf($auth_user));

		$this->assertEquals(1, $auth_user->subscriptions_count);
		$this->assertEquals(0, $auth_user->subscribers_count);
		$this->assertEquals(1, $user->subscribers_count);
		$this->assertEquals(0, $user->subscriptions_count);
		$this->assertEquals(0, $auth_user->friends_count);
		$this->assertEquals(0, $auth_user->friends_count);

		Notification::assertNotSentTo($auth_user, NewSubscriberNotification::class);

		Notification::assertSentTo(
			$user,
			NewSubscriberNotification::class,
			function ($notification, $channels) use ($auth_user) {
				$this->assertNotContains('mail', $channels);
				$this->assertContains('database', $channels);

				$data = $notification->toArray($auth_user);

				$this->assertEquals(__('notification.new_subscriber.subject'), $data['title']);
				$this->assertEquals(__('notification.new_subscriber.line', [
					'userName' => $auth_user->userName
				]), $data['description']);

				$this->assertEquals(route('profile', $auth_user), $data['url']);

				return $notification->subscriber->id == $auth_user->id;
			}
		);
	}

	public function testUnsubscribeHttp()
	{
		$auth_user = User::factory()->create();

		$user = User::factory()->create();

		$user_relation = UserRelation::factory()->create([
				'user_id' => $auth_user->id,
				'user_id2' => $user->id,
				'status' => UserRelationType::Subscriber
			]);

		$auth_user->refresh();
		$user->refresh();

		$this->assertTrue($auth_user->isSubscriberOf($user));

		$response = $this->actingAs($auth_user)
			->get(route('users.unsubscribe', $user));
		//dump(session('errors'));
		$response->assertSessionHasNoErrors()
			->assertRedirect();

		$auth_user->refresh();
		$user->refresh();

		$this->assertFalse($auth_user->isSubscriberOf($user));
		$this->assertFalse($user->isSubscriberOf($auth_user));
		$this->assertFalse($auth_user->isFriendOf($user));
		$this->assertFalse($user->isFriendOf($auth_user));

		$this->assertEquals(0, $auth_user->subscriptions_count);
		$this->assertEquals(0, $auth_user->subscribers_count);
		$this->assertEquals(0, $user->subscribers_count);
		$this->assertEquals(0, $user->subscriptions_count);
		$this->assertEquals(0, $auth_user->friends_count);
		$this->assertEquals(0, $auth_user->friends_count);
	}

	public function testUnsubscribeIfFriends()
	{
		$auth_user = User::factory()->create();

		$user = User::factory()->create();

		$user_relation = UserRelation::factory()->create([
				'user_id' => $auth_user->id,
				'user_id2' => $user->id,
				'status' => UserRelationType::Friend
			]);

		$response = $this->actingAs($auth_user)
			->get(route('users.unsubscribe', $user));
		//dump(session('errors'));
		$response->assertSessionHasNoErrors()
			->assertRedirect();

		$auth_user->refresh();
		$user->refresh();

		$this->assertFalse($auth_user->isSubscriberOf($user));
		$this->assertTrue($user->isSubscriberOf($auth_user));
		$this->assertFalse($auth_user->isFriendOf($user));
		$this->assertFalse($user->isFriendOf($auth_user));

		$this->assertEquals(0, $auth_user->subscriptions_count);
		$this->assertEquals(1, $auth_user->subscribers_count);
		$this->assertEquals(0, $user->subscribers_count);
		$this->assertEquals(1, $user->subscriptions_count);
		$this->assertEquals(0, $auth_user->friends_count);
		$this->assertEquals(0, $auth_user->friends_count);
	}

	public function testSubscribeIfUserSubscriberHttp()
	{
		$auth_user = User::factory()->create();

		$user = User::factory()->create();

		$user_relation = UserRelation::factory()->create([
				'user_id' => $user->id,
				'user_id2' => $auth_user->id,
				'status' => UserRelationType::Subscriber
			]);

		$response = $this->actingAs($auth_user)
			->get(route('users.subscribe', $user));
		//dump(session('errors'));
		$response->assertSessionHasNoErrors()
			->assertRedirect();

		$auth_user->refresh();
		$user->refresh();

		$this->assertFalse($auth_user->isSubscriberOf($user));
		$this->assertFalse($user->isSubscriberOf($auth_user));
		$this->assertTrue($auth_user->isFriendOf($user));
		$this->assertTrue($user->isFriendOf($auth_user));

		$this->assertEquals(0, $auth_user->subscriptions_count);
		$this->assertEquals(0, $auth_user->subscribers_count);
		$this->assertEquals(0, $user->subscribers_count);
		$this->assertEquals(0, $user->subscriptions_count);
		$this->assertEquals(1, $auth_user->friends_count);
		$this->assertEquals(1, $auth_user->friends_count);
	}
}
