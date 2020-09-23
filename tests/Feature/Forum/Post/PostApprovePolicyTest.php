<?php

namespace Tests\Feature\Forum\Post;

use App\Post;
use App\User;
use Tests\TestCase;

class PostApprovePolicyTest extends TestCase
{
	public function testCanIfHasPermission()
	{
		$post = factory(Post::class)
			->states('sent_for_review')
			->create();

		$user = factory(User::class)->create();
		$user->group->check_post_comments = true;
		$user->push();

		$this->assertTrue($user->can('approve', $post));
	}

	public function testCantIfDoesntHavePermission()
	{
		$post = factory(Post::class)
			->states('sent_for_review')
			->create();

		$user = factory(User::class)->create();
		$user->group->check_post_comments = false;
		$user->push();

		$this->assertFalse($user->can('approve', $post));
	}

	public function testCantIfPostNotOnReview()
	{
		$post = factory(Post::class)
			->create();

		$user = factory(User::class)
			->states('admin')
			->create();

		$this->assertFalse($user->can('approve', $post));
	}
}
