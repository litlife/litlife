<?php

namespace Tests\Feature\Forum\Topic;

use App\Topic;
use App\User;
use Tests\TestCase;

class TopicArchiveTest extends TestCase
{
	public function testIndexHttp()
	{
		Topic::archived()->delete();

		$user = factory(User::class)
			->create();

		$topic = factory(Topic::class)
			->states('archived')
			->create();

		$this->actingAs($user)
			->get(route('topics.archived'))
			->assertOk()
			->assertSeeText(__('topic.archived_topics'))
			->assertSeeText($topic->name);
	}

	public function testArchive()
	{
		$user = factory(User::class)->create();
		$user->group->manipulate_topic = true;
		$user->push();

		$topic = factory(Topic::class)
			->create();

		$this->actingAs($user)
			->get(route('topics.archive', ['topic' => $topic]))
			->assertRedirect();

		$topic->refresh();

		$this->assertTrue($topic->isArchived());
	}

	public function testUnarchive()
	{
		$user = factory(User::class)->create();
		$user->group->manipulate_topic = true;
		$user->push();

		$topic = factory(Topic::class)
			->states('archived')
			->create();

		$this->actingAs($user)
			->get(route('topics.unarchive', ['topic' => $topic]))
			->assertRedirect();

		$topic->refresh();

		$this->assertFalse($topic->isArchived());
	}
}
