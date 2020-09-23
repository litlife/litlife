<?php

namespace Tests\Feature\Forum\Topic;

use App\Forum;
use App\Topic;
use App\User;
use Tests\TestCase;

class TopicCreatePolicyTest extends TestCase
{
	public function testTrue()
	{
		$user = factory(User::class)->create();
		$user->group->add_forum_topic = true;
		$user->push();

		$this->assertTrue($user->can('create', Topic::class));
	}

	public function testFalse()
	{
		$user = factory(User::class)
			->create();

		$topic = factory(Topic::class)
			->create();

		$this->assertFalse($user->can('create', Topic::class));
	}

	public function testCanIfForumPrivateAndUserInList()
	{
		$forum = factory(Forum::class)
			->states('private', 'with_user_access')
			->create(['min_message_count' => 0]);

		$user = $forum->users_with_access->first();
		$user->group->add_forum_topic = true;
		$user->push();

		$this->assertTrue($user->can('create_topic', $forum));
	}

	public function testCantIfForumPrivateAndUserNotInList()
	{
		$forum = factory(Forum::class)
			->states('private', 'with_user_access')
			->create(['min_message_count' => 0]);

		$user = factory(User::class)->create();
		$user->group->add_forum_topic = true;
		$user->push();

		$this->assertFalse($user->can('create_topic', $forum));
	}


	public function testCanCreateTopicIfHasPermissions()
	{
		$user = factory(User::class)->create();
		$user->group->add_forum_topic = true;
		$user->push();

		$forum = factory(Forum::class)
			->create(['min_message_count' => 0]);

		$this->assertTrue($user->can('create_topic', $forum));
	}

	public function testCantCreateTopicIfNoPermissions()
	{
		$user = factory(User::class)->create();
		$user->group->add_forum_topic = false;
		$user->push();

		$forum = factory(Forum::class)
			->create(['min_message_count' => 0]);

		$this->assertFalse($user->can('create_topic', $forum));
	}

	public function testCantCreateIfTopicIfIfThereAreNotEnoughMessages()
	{
		$user = factory(User::class)->create();
		$user->group->add_forum_topic = true;
		$user->forum_message_count = 30;
		$user->push();

		$forum = factory(Forum::class)
			->create(['min_message_count' => 40]);

		$this->assertFalse($user->can('create_topic', $forum));
	}
}
