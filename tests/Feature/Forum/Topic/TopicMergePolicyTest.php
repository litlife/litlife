<?php

namespace Tests\Feature\Forum\Topic;

use App\Topic;
use App\User;
use Tests\TestCase;

class TopicMergePolicyTest extends TestCase
{
	public function testTrue()
	{
		$user = User::factory()->create();
		$user->group->manipulate_topic = true;
		$user->push();

		$topic = Topic::factory()->create();

		$this->assertTrue($user->can('merge', $topic));
	}

	public function testFalse()
	{
		$user = User::factory()->create();
		$user->group->manipulate_topic = false;
		$user->push();

		$topic = Topic::factory()->create();

		$this->assertFalse($user->can('merge', $topic));
	}
}
