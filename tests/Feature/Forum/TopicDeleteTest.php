<?php

namespace Tests\Feature\Forum;

use App\Forum;
use App\Jobs\Forum\UpdateForumCounters;
use App\Post;
use App\Topic;
use App\User;
use Carbon\Carbon;
use Tests\TestCase;

class TopicDeleteTest extends TestCase
{
	public function testDelete()
	{
		$user = factory(User::class)
			->states('admin')
			->create();

		$post = factory(Post::class)->create();

		$topic = $post->topic;

		$this->assertEquals(1, $topic->create_user->topics_count);
		$this->assertEquals(1, $topic->forum->topic_count);

		$response = $this->actingAs($user)
			->delete(route('topics.destroy', $topic))
			->assertOk();

		$topic->refresh();
		$post->refresh();

		$this->assertTrue($topic->trashed());
		$this->assertFalse($post->trashed());

		$response->assertJsonFragment($topic->toArray());
		$this->assertEquals(0, $topic->create_user->topics_count);
		$this->assertEquals(0, $topic->forum->topic_count);
	}

	public function testRestore()
	{
		$user = factory(User::class)
			->states('admin')
			->create();

		$post = factory(Post::class)->create();

		$topic = $post->topic;

		$topic->delete();

		$this->assertTrue($topic->trashed());
		$this->assertEquals(0, $topic->create_user->topics_count);
		$this->assertEquals(0, $topic->forum->topic_count);

		$response = $this->actingAs($user)
			->delete(route('topics.destroy', $topic))
			->assertOk();

		$topic->refresh();
		$post->refresh();

		$this->assertFalse($topic->trashed());
		$this->assertFalse($post->trashed());
		$this->assertEquals(1, $topic->create_user->topics_count);
		$this->assertEquals(1, $topic->forum->topic_count);
	}

	public function testUpdateForumCounters()
	{
		$user = factory(User::class)
			->states('admin')
			->create();

		$forum = factory(Forum::class)
			->create()->fresh();

		$topic = factory(Topic::class)
			->create(['forum_id' => $forum->id]);

		$post = factory(Post::class)
			->create(['topic_id' => $topic->id]);

		Carbon::setTestNow(now()->addMinute());

		$topic2 = factory(Topic::class)
			->create(['forum_id' => $forum->id]);

		$post2 = factory(Post::class)
			->create(['topic_id' => $topic2->id]);

		UpdateForumCounters::dispatch($forum);

		$forum->refresh();

		$this->assertEquals(2, $forum->topic_count);
		$this->assertEquals(2, $forum->post_count);
		$this->assertEquals($post2->id, $forum->last_post_id);
		$this->assertEquals($topic2->id, $forum->last_topic_id);

		$response = $this->actingAs($user)
			->delete(route('topics.destroy', $topic2))
			->assertOk();

		$forum->refresh();

		$this->assertEquals(1, $forum->topic_count);
		$this->assertEquals(1, $forum->post_count);
		$this->assertEquals($post->id, $forum->last_post_id);
		$this->assertEquals($topic->id, $forum->last_topic_id);
	}
}
