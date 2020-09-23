<?php

namespace Tests\Feature\Forum\Post;

use App\Post;
use App\Topic;
use App\User;
use Tests\TestCase;

class PostMoveTest extends TestCase
{
	public function testTransfer()
	{
		$user = factory(User::class)->create();
		$user->group->forum_move_post = true;
		$user->push();

		$post = factory(Post::class)
			->create()
			->fresh();

		$post2 = factory(Post::class)
			->create(['topic_id' => $post->topic_id])
			->fresh();

		$post3 = factory(Post::class)
			->create()
			->fresh();

		$post4 = factory(Post::class)
			->create(['topic_id' => $post3->topic_id])
			->fresh();

		$topic = $post->topic;
		$topic2 = $post3->topic;
		/*
				dump($post->id);
				dump($post2->id);
				dump($post3->id);
		*/
		$response = $this->actingAs($user)
			->post(route('posts.transfer', ['topic_id' => $topic->id]),
				['posts' => [
					$post2->id,
					$post3->id,
					$post4->id
				]]
			)
			->assertSessionHasNoErrors()
			->assertRedirect(route('topics.show', ['topic' => $topic->id]));

		$topic->refresh();
		$topic2->refresh();
		/*
				dump($post->fresh()->topic_id.' '.$post->fresh()->forum_id);
				dump($post2->fresh()->topic_id.' '.$post2->fresh()->forum_id);
				dump($post3->fresh()->topic_id.' '.$post3->fresh()->forum_id);
		*/
		$this->assertEquals($post4->id, $topic->last_post_id);
		$this->assertEquals($post4->created_at, $topic->last_post_created_at);

		$this->assertEquals(4, $topic->posts()->count());
		$this->assertEquals(4, $topic->post_count);

		$forum = $topic->forum->fresh();

		$this->assertEquals(4, $forum->posts()->count());
		$this->assertEquals(4, $forum->post_count);
		$this->assertEquals(1, $forum->topic_count);

		$this->assertEquals(0, $topic2->posts()->count());
		$this->assertEquals(0, $topic2->post_count);
		$this->assertEquals(1, $topic2->forum->topic_count);

		$this->assertEquals(null, $topic2->last_post_id);
		$this->assertEquals(null, $topic2->last_post_created_at);
	}

	public function testMovePostsHttp()
	{
		$user = factory(User::class)->create();
		$user->group->forum_move_post = true;
		$user->push();

		$post = factory(Post::class)
			->create();

		$post2 = factory(Post::class)
			->create();

		$post3 = factory(Post::class)
			->create();

		$this->actingAs($user)
			->get(route('posts.move',
				['ids' => implode(',', [$post->id, $post2->id, $post3->id])]))
			->assertOk();

		$topic = factory(Topic::class)
			->create();

		$response = $this->actingAs($user)
			->post(route('posts.transfer',
				[
					'topic_id' => $topic->id,
					'posts' => [$post->id, $post2->id, $post3->id]
				]))
			->assertRedirect();

		//dump(session('errors'));

		$response->assertSessionHasNoErrors()
			->assertRedirect(route('topics.show', $topic->id));

		$this->assertEquals($topic->id, $post->fresh()->topic_id);
		$this->assertEquals($topic->id, $post2->fresh()->topic_id);
		$this->assertEquals($topic->id, $post3->fresh()->topic_id);
		$this->assertEquals(3, $topic->fresh()->post_count);
		$this->assertEquals(3, $topic->fresh()->forum->post_count);
	}

	public function testTransferIfPostsFixed()
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
			->post(route('posts.move'),
				[
					'topic_id' => $topic->id,
					'posts' => [$post2->id]
				])
			->assertSessionHasNoErrors()
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
