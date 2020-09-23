<?php

namespace Tests\Feature\Forum\Topic;

use App\Topic;
use App\User;
use Tests\TestCase;

class TopicEditPolicyTest extends TestCase
{
	public function testUpdatePolicy()
	{
		$user = factory(User::class)->states('with_user_group')->create();
		$user->group->edit_forum_self_topic = true;
		$user->push();

		$user2 = factory(User::class)->states('with_user_group')->create();

		$topic = factory(Topic::class)->create();
		$topic->create_user_id = $user->id;
		$topic->push();

		$this->assertTrue($user->can('update', $topic));
		$this->assertFalse($user2->can('update', $topic));

		//

		$user = factory(User::class)->create();
		$user->group->edit_forum_self_topic = true;
		$user->group->edit_forum_other_user_topic = true;
		$user->push();

		$user2 = factory(User::class)->states('with_user_group')->create();

		$topic = factory(Topic::class)->create();
		$topic->create_user_id = $user->id;
		$topic->push();

		$topic2 = factory(Topic::class)->create();
		$topic2->create_user_id = $user2->id;
		$topic2->push();

		$this->assertTrue($user->can('update', $topic));
		$this->assertTrue($user->can('update', $topic2));
		$this->assertFalse($user2->can('update', $topic));
		$this->assertFalse($user2->can('update', $topic2));
	}
}
