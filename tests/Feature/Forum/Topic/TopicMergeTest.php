<?php

namespace Tests\Feature\Forum\Topic;

use App\Jobs\Forum\UpdateForumCounters;
use App\Post;
use App\User;
use Tests\TestCase;

class TopicMergeTest extends TestCase
{
	public function testMergeHttp()
	{
		$user = factory(User::class)->create();
		$user->group->manipulate_topic = true;
		$user->push();

		$post = factory(Post::class)->create();
		$post2 = factory(Post::class)->create();
		$post3 = factory(Post::class)->create();
		$post4 = factory(Post::class)->create();

		$topic = $post->topic;
		$topic4 = $post4->topic;
		$forum4 = $topic4->forum;

		UpdateForumCounters::dispatch($post->topic->forum);

		$response = $this->actingAs($user)
			->post(route('topics.merge', ['topic' => $topic->id]),
				[
					'topics' => [
						$post2->topic->id,
						$post3->topic->id,
						$post4->topic->id
					]
				]
			)
			->assertSessionHasNoErrors()
			->assertRedirect(route('topics.show', ['forum' => $post->topic->forum->id, 'topic' => $post->topic->id]));

		$topic->refresh();
		$topic4->refresh();

		$this->assertSoftDeleted($topic4);

		$this->assertEquals($post4->id, $topic->last_post_id);
		$this->assertEquals($post4->created_at, $topic->last_post_created_at);

		$this->assertEquals(4, $topic->posts()->count());
		$this->assertEquals(4, $topic->post_count);

		$forum = $topic->forum->fresh();

		$this->assertEquals(4, $forum->posts()->count());
		$this->assertEquals(4, $forum->post_count);
		$this->assertEquals(1, $forum->topic_count);

		$forum4->refresh();

		$this->assertEquals(0, $forum4->posts()->count());
		$this->assertEquals(0, $forum4->topics()->count());
		$this->assertEquals(0, $forum4->topic_count);
		$this->assertEquals(0, $forum4->post_count);
		$this->assertNull($forum4->last_post_id);
		$this->assertNull($forum4->last_topic_id);
	}

	public function testMergeTopics()
	{
		$admin = factory(User::class)
			->states('admin')
			->create();

		$post = factory(Post::class)->create();
		$topic = $post->topic;
		$post->fix();

		$post2 = factory(Post::class)->create();
		$topic2 = $post2->topic;
		$post2->fix();

		$this->actingAs($admin)
			->post(route('topics.merge', ['topic' => $topic]),
				['topics' => [$topic2->id]])
			->assertRedirect();

		$topic->refresh();
		$topic2->refresh();
		$post->refresh();
		$post2->refresh();

		$this->assertTrue($post->isFixed());
		$this->assertFalse($post2->isFixed());

		$this->assertEquals($post->id, $topic->top_post_id);
		$this->assertNull($topic2->top_post_id);

		$this->assertEquals($topic->id, $post->topic_id);
		$this->assertEquals($topic->id, $post2->topic_id);
	}
}
