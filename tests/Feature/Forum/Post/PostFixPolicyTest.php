<?php

namespace Tests\Feature\Forum\Post;

use App\Post;
use App\User;
use Tests\TestCase;

class PostFixPolicyTest extends TestCase
{
	public function testCanIfHasPermission()
	{
		$user = factory(User::class)->create();
		$user->group->forum_post_manage = true;
		$user->push();

		$post = factory(Post::class)->create();

		$this->assertTrue($user->can('fix', $post));
	}

	public function testCantIfDoesntHavePermission()
	{
		$user = factory(User::class)->create();
		$user->group->forum_post_manage = false;
		$user->push();

		$post = factory(Post::class)->create();

		$this->assertFalse($user->can('fix', $post));
	}

	public function testCantIfChild()
	{
		$user = factory(User::class)->create();
		$user->group->forum_post_manage = true;
		$user->push();

		$parent = factory(Post::class)->create();

		$post = factory(Post::class)->create(['parent' => $parent]);

		$this->assertFalse($user->can('fix', $post));
	}

	public function testCantIfFixed()
	{
		$user = factory(User::class)->create();
		$user->group->forum_post_manage = true;
		$user->push();

		$post = factory(Post::class)->create();
		$post->fix();
		$post->refresh();

		$this->assertFalse($user->can('fix', $post));
	}
}
