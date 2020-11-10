<?php

namespace Tests\Feature\Forum\Topic;

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
		$user = User::factory()->admin()->create();

		$forum = Forum::factory()->create();

		$topic = Topic::factory()->with_post()->create();
		$topic->forum()->associate($forum);
		$topic->create_user()->associate($user);
		$topic->save();
		$topic->refresh();

		Carbon::setTestNow(now()->addMinute());

		$post = $topic->posts()->first();

		$topic2 = Topic::factory()->with_post()->create();
		$topic2->forum()->associate($forum);
		$topic2->create_user()->associate($user);
		$topic2->save();
		$topic2->refresh();

		$post2 = $topic2->posts()->first();

		$response = $this->actingAs($user)
			->delete(route('topics.destroy', $topic2))
			->assertOk();

		$forum->refresh();
		$topic2->refresh();
		$post2->refresh();
		$user->refresh();

		$this->assertTrue($topic2->trashed());
		$this->assertTrue($post2->trashed());

		$this->assertEquals(1, $user->topics_count);

		$response->assertJsonFragment($topic2->toArray());
		$this->assertEquals(1, $forum->topic_count);

		$this->assertEquals($post->id, $forum->last_post_id);
		$this->assertEquals($topic->id, $forum->last_topic_id);
	}

	public function testUpdateForumCounters()
	{
		$user = User::factory()->admin()->create();

		$forum = Forum::factory()->create()->fresh();

		$topic = Topic::factory()->create(['forum_id' => $forum->id]);

		$post = Post::factory()->create(['topic_id' => $topic->id]);

		Carbon::setTestNow(now()->addMinute());

		$topic2 = Topic::factory()->create(['forum_id' => $forum->id]);

		$post2 = Post::factory()->create(['topic_id' => $topic2->id]);

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

	public function testPostsDeletedAlongWithTopic()
	{
		$topic = Topic::factory()->create();

		$post = factory(Post::class)
			->make();

		$topic->posts()->save($post);

		$topic->delete();

		$post->refresh();
		$topic->refresh();

		$this->assertSoftDeleted($topic);
		$this->assertSoftDeleted($post);
	}
}
