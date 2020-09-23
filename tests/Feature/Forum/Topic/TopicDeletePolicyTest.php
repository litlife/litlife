<?php

namespace Tests\Feature\Forum\Topic;

use App\Topic;
use App\User;
use Tests\TestCase;

class TopicDeletePolicyTest extends TestCase
{
	public function testDeletePolicy()
	{
		$user = factory(User::class)->states('with_user_group')->create();
		$user->group->delete_forum_self_topic = true;
		$user->push();

		$user2 = factory(User::class)->states('with_user_group')->create();

		$topic = factory(Topic::class)->create();
		$topic->create_user_id = $user->id;
		$topic->push();

		$this->assertTrue($user->can('delete', $topic));
		$this->assertFalse($user2->can('delete', $topic));

		//

		$user = factory(User::class)->states('with_user_group')->create();
		$user->group->delete_forum_self_topic = true;
		$user->group->delete_forum_other_user_topic = true;
		$user->push();

		$user2 = factory(User::class)->states('with_user_group')->create();

		$topic = factory(Topic::class)->create();
		$topic->create_user_id = $user->id;
		$topic->push();

		$topic2 = factory(Topic::class)->create();
		$topic2->create_user_id = $user2->id;
		$topic2->push();

		$this->assertTrue($user->can('delete', $topic));
		$this->assertTrue($user->can('delete', $topic2));
		$this->assertFalse($user2->can('delete', $topic));
		$this->assertFalse($user2->can('delete', $topic2));
	}

	public function testRestorePolicy()
	{
		$user = factory(User::class)->states('with_user_group')->create();
		$user->group->delete_forum_self_topic = true;
		$user->push();

		$user2 = factory(User::class)->create();
		$user2->push();

		$topic = factory(Topic::class)->create();
		$topic->create_user_id = $user->id;
		$topic->push();
		$topic->delete();

		$this->assertTrue($user->can('restore', $topic));
		$this->assertFalse($user2->can('restore', $topic));

		//

		$user = factory(User::class)->create();
		$user->group->delete_forum_self_topic = true;
		$user->group->delete_forum_other_user_topic = true;
		$user->push();

		$user2 = factory(User::class)->states('with_user_group')->create();

		$topic = factory(Topic::class)->create();
		$topic->create_user_id = $user->id;
		$topic->push();
		$topic->delete();

		$topic2 = factory(Topic::class)->create();
		$topic2->create_user_id = $user2->id;
		$topic2->push();
		$topic2->delete();

		$this->assertTrue($user->can('restore', $topic));
		$this->assertTrue($user->can('restore', $topic2));
		$this->assertFalse($user2->can('restore', $topic));
		$this->assertFalse($user2->can('restore', $topic2));
	}
}
