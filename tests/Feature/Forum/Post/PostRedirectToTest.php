<?php

namespace Tests\Feature\Forum\Post;

use App\Post;
use Tests\TestCase;

class PostRedirectToTest extends TestCase
{
	public function testGoToIfOnReview()
	{
		$post = factory(Post::class)->create();
		$post->statusSentForReview();
		$post->save();

		$this->followingRedirects()
			->get(route('posts.go_to', $post))
			->assertOk();
	}
}
