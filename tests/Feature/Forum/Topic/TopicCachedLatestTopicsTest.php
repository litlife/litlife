<?php

namespace Tests\Feature\Forum\Topic;

use App\Post;
use App\Topic;
use Tests\TestCase;

class TopicCachedLatestTopicsTest extends TestCase
{
	public function testCachedLatestTopicsPublicScopeAttached()
	{
		$post = Post::factory()->create();

		$topic = $post->topic;
		$forum = $topic->forum;

		Topic::refreshLatestTopics();
		$topics = Topic::cachedLatestTopics();

		$this->assertEquals($topic->id, $topics->first()->id);

		$forum->private = true;
		$forum->save();

		Topic::refreshLatestTopics();
		$topics = Topic::cachedLatestTopics();

		if (!empty($topics->first()))
			$this->assertNotEquals($topic->id, $topics->first()->id);
	}
}
