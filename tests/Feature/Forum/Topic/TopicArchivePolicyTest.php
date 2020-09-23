<?php

namespace Tests\Feature\Forum\Topic;

use App\Topic;
use App\User;
use Tests\TestCase;

class TopicArchivePolicyTest extends TestCase
{
	public function testCanArchiveIfHasPermission()
	{
		$user = factory(User::class)->create();
		$user->group->manipulate_topic = true;
		$user->push();

		$topic = factory(Topic::class)
			->create();

		$this->assertTrue($user->can('archive', $topic));
	}

	public function testCantArchiveIfNoPermission()
	{
		$user = factory(User::class)->create();
		$user->group->manipulate_topic = false;
		$user->push();

		$topic = factory(Topic::class)
			->create();

		$this->assertFalse($user->can('archive', $topic));
	}

	public function testCantArchiveIfAlreadyArchived()
	{
		$user = factory(User::class)->create();
		$user->group->manipulate_topic = true;
		$user->push();

		$topic = factory(Topic::class)
			->states('archived')
			->create();

		$this->assertFalse($user->can('archive', $topic));
	}
}
