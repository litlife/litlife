<?php

namespace Tests\Feature\Forum\Post;

use App\Post;
use App\User;
use Tests\TestCase;

class PostApproveTest extends TestCase
{
	public function testApprove()
	{
		$user = factory(User::class)->create();
		$user->group->check_post_comments = true;
		$user->push();

		foreach (Post::sentOnReview()->get() as $post) {
			$post->forceDelete();
		}

		$this->assertEquals(0, Post::getCachedOnModerationCount());

		$post = factory(Post::class)->create();
		$post->statusSentForReview();
		$post->save();

		Post::flushCachedOnModerationCount();
		$this->assertEquals(1, Post::getCachedOnModerationCount());

		$this->actingAs($user)
			->get(route('posts.approve', ['post' => $post]))
			->assertOk();

		$this->assertTrue($post->fresh()->isAccepted());

		$this->assertEquals(0, Post::getCachedOnModerationCount());
	}
}
