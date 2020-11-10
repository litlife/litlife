<?php

namespace Tests\Feature\Forum\Topic;

use App\Topic;
use Tests\TestCase;

class TopicIndexTest extends TestCase
{
	public function testSearch()
	{
		$topic = Topic::factory()->create(['name' => uniqid()]);

		$this->get(route('topics.index', ['search_str' => $topic->name]))
			->assertOk()
			->assertSeeText($topic->name)
			->assertDontSeeText(__('topic.nothing_found'));
	}
}
