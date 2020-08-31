<?php

namespace Tests\Feature\Forum;

use App\Topic;
use App\User;
use Tests\TestCase;

class TopicPolicyTest extends TestCase
{
	public function testCreate()
	{
		$user = factory(User::class)
			->create();

		$topic = new Topic();

		$this->assertFalse($user->can('create', $topic));

		$user->group->add_forum_topic = true;
		$user->push();

		$this->assertTrue($user->can('create', $topic));
	}
}
