<?php

namespace Tests\Feature\Forum\Post;

use App\Post;
use App\User;
use Carbon\Carbon;
use Tests\TestCase;

class PostDeleteTest extends TestCase
{
	public function testDeleteHttp()
	{
		$user = User::factory()->create();
		$user->group->forum_delete_self_post = true;
		$user->group->forum_delete_other_user_post = true;
		$user->push();

		$post = Post::factory()->create()
			->fresh();

		Carbon::setTestNow(now()->addMinute());

		$post2 = Post::factory()->create(['topic_id' => $post->topic_id])
			->fresh();

		$create_user = $post2->create_user;

		$this->assertEquals(1, $create_user->forum_message_count);

		$response = $this->actingAs($user)
			->delete(route('posts.destroy', ['post' => $post2]))
			->assertOk();

		$post2->refresh();
		$create_user->refresh();

		$this->assertTrue($post2->trashed());
		$this->assertEquals(0, $create_user->forum_message_count);

		$topic = $post->topic;

		$this->assertNotNull($post);

		$this->assertEquals($post->id, $topic->last_post_id);
		$this->assertEquals($post->created_at, $topic->last_post_created_at);
		$this->assertEquals(1, $topic->post_count);

		$forum = $topic->forum;

		$this->assertEquals(1, $forum->topic_count);
		$this->assertEquals(1, $forum->post_count);
		$this->assertEquals($topic->id, $forum->last_topic_id);
		$this->assertEquals($post->id, $forum->last_post_id);
	}

	public function testDeleteReply()
	{
		$user = User::factory()->create();
		$user->group->forum_delete_self_post = true;
		$user->group->forum_delete_other_user_post = true;
		$user->push();

		$post = Post::factory()->create()
			->fresh();

		$reply = Post::factory()->create([
				'parent' => $post->id,
				'topic_id' => $post->topic_id
			])
			->fresh();

		$post->refresh();

		$this->assertEquals(1, $post->children_count);

		$response = $this->actingAs($user)
			->delete(route('posts.destroy', ['post' => $reply]))
			->assertSessionHasNoErrors()
			->assertOk();

		$post->refresh();
		$reply->refresh();

		$this->assertTrue($reply->trashed());

		$this->assertEquals(0, $post->children_count);
	}

	public function testDeleteIfCreateUserDeleted()
	{
		$post = Post::factory()->create();

		$this->assertEquals(1, $post->create_user->forum_message_count);

		$create_user = $post->create_user;
		$create_user->delete();
		$create_user->refresh();
		$post->refresh();

		$this->assertSoftDeleted($post->create_user);

		$post->delete();
		$post->refresh();
		$create_user->refresh();

		$this->assertTrue($post->trashed());
		$this->assertNotNull($post->deleted_at);

		$this->assertEquals(0, $post->create_user()->any()->first()->forum_message_count);
	}

	public function testDeleteIfTopicDeleted()
	{
		$post = Post::factory()->create();

		$topic = $post->topic;
		$topic->delete();
		$post->refresh();

		$this->assertTrue($topic->trashed());

		$post->delete();
		$post->refresh();

		$this->assertTrue($post->trashed());
	}

	public function testDeleteIfForumDeleted()
	{
		$post = Post::factory()->create();

		$forum = $post->forum;
		$forum->delete();
		$post->refresh();

		$this->assertTrue($forum->trashed());

		$post->delete();
		$post->refresh();

		$this->assertTrue($post->trashed());
	}

	public function testDecline()
	{
		$user = User::factory()->create();
		$user->group->forum_delete_self_post = true;
		$user->group->forum_delete_other_user_post = true;
		$user->push();

		foreach (Post::sentOnReview()->get() as $post) {
			$post->forceDelete();
		}

		$this->assertEquals(0, Post::getCachedOnModerationCount());

		$post = Post::factory()->create();
		$post->statusSentForReview();
		$post->save();

		Post::flushCachedOnModerationCount();
		$this->assertEquals(1, Post::getCachedOnModerationCount());

		$this->actingAs($user)
			->delete(route('posts.destroy', ['post' => $post]))
			->assertOk();

		$this->assertTrue($post->fresh()->trashed());

		$this->assertEquals(0, Post::getCachedOnModerationCount());
	}
}
