<?php

namespace Tests\Feature\Forum\Topic;

use App\Topic;
use App\User;
use Tests\TestCase;

class TopicMovePolicyTest extends TestCase
{
	public function testTrue()
	{
		$user = factory(User::class)->create();
		$user->group->manipulate_topic = true;
		$user->push();

		$topic = factory(Topic::class)->create();

		$this->assertTrue($user->can('move', $topic));
	}

	public function testFalse()
	{
		$user = factory(User::class)->create();
		$user->group->manipulate_topic = false;
		$user->push();

		$topic = factory(Topic::class)->create();

		$this->assertFalse($user->can('move', $topic));
	}
}
