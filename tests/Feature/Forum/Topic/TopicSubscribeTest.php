<?php

namespace Tests\Feature\Forum\Topic;

use App\Topic;
use App\User;
use App\UserTopicSubscription;
use Tests\TestCase;

class TopicSubscribeTest extends TestCase
{
	public function testSubscribeAjax()
	{
		$topic = factory(Topic::class)
			->create();

		$user = factory(User::class)
			->create();

		$this->actingAs($user)
			->get(route('topics.subscribe', ['topic' => $topic]),
				['HTTP_X-Requested-With' => 'XMLHttpRequest'])
			->assertOk()
			->assertJsonFragment(['status' => 'subscribed']);

		$subscription = $user->subscribed_topics()->first();

		$this->assertEquals($user->id, $subscription->user_id);
		$this->assertEquals($topic->id, $subscription->topic_id);
	}

	public function testUnsubscribeAjax()
	{
		$subscription = factory(UserTopicSubscription::class)
			->create();

		$user = $subscription->user;
		$topic = $subscription->topic;

		$this->actingAs($user)
			->get(route('topics.unsubscribe', ['topic' => $topic]),
				['HTTP_X-Requested-With' => 'XMLHttpRequest'])
			->assertOk()
			->assertJsonFragment(['status' => 'unsubscribed']);

		$subscription = $user->subscribed_topics()->first();

		$this->assertNull($subscription);
		$this->assertEquals(0, $user->subscribed_topics()->count());
	}

	public function testUnsubscribe()
	{
		$subscription = factory(UserTopicSubscription::class)
			->create();

		$user = $subscription->user;
		$topic = $subscription->topic;

		$this->actingAs($user)
			->get(route('topics.unsubscribe', ['topic' => $topic]))
			->assertOk()
			->assertSeeText(__('topic.you_have_successfully_unsubscribed_from_receiving_notifications_of_new_messages'));
	}

}
