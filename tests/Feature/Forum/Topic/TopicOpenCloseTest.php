<?php

namespace Tests\Feature\Forum\Topic;

use App\Topic;
use App\User;
use Tests\TestCase;

class TopicOpenCloseTest extends TestCase
{
	public function testOpenClosePolicy()
	{
		$user = factory(User::class)
			->states('with_user_group')
			->create();
		$user->group->manipulate_topic = true;
		$user->push();

		$user2 = factory(User::class)
			->states('with_user_group')
			->create();

		$opened_topic = factory(Topic::class)
			->create();

		$closed_topic = factory(Topic::class)
			->states('closed')
			->create();

		$this->assertTrue($user->can('open', $closed_topic));
		$this->assertFalse($user2->can('open', $closed_topic));
		$this->assertFalse($user->can('open', $opened_topic));
		$this->assertFalse($user2->can('open', $opened_topic));

		$this->assertTrue($user->can('close', $opened_topic));
		$this->assertFalse($user2->can('close', $opened_topic));
		$this->assertFalse($user->can('close', $closed_topic));
		$this->assertFalse($user2->can('close', $closed_topic));
	}

	public function testOpen()
	{
		$topic = factory(Topic::class)
			->states('closed')
			->create();

		$user = factory(User::class)
			->states('administrator')
			->create();

		$this->actingAs($user)
			->get(route('topics.open', $topic))
			->assertRedirect();

		$topic->refresh();

		$this->assertFalse($topic->isClosed());
	}

	public function testClose()
	{
		$topic = factory(Topic::class)
			->create();

		$user = factory(User::class)
			->states('administrator')
			->create();

		$this->actingAs($user)
			->get(route('topics.close', $topic))
			->assertRedirect();

		$topic->refresh();

		$this->assertTrue($topic->isClosed());
	}
}
