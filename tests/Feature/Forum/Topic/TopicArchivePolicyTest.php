<?php

namespace Tests\Feature\Forum\Topic;

use App\Topic;
use App\User;
use Tests\TestCase;

class TopicArchivePolicyTest extends TestCase
{
	public function testCanArchiveIfHasPermission()
	{
		$user = User::factory()->create();
		$user->group->manipulate_topic = true;
		$user->push();

		$topic = Topic::factory()->create();

		$this->assertTrue($user->can('archive', $topic));
	}

	public function testCantArchiveIfNoPermission()
	{
		$user = User::factory()->create();
		$user->group->manipulate_topic = false;
		$user->push();

		$topic = Topic::factory()->create();

		$this->assertFalse($user->can('archive', $topic));
	}

	public function testCantArchiveIfAlreadyArchived()
	{
		$user = User::factory()->create();
		$user->group->manipulate_topic = true;
		$user->push();

		$topic = Topic::factory()->archived()->create();

		$this->assertFalse($user->can('archive', $topic));
	}
}
