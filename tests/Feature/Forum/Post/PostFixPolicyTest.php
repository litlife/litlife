<?php

namespace Tests\Feature\Forum\Post;

use App\Post;
use App\User;
use Tests\TestCase;

class PostFixPolicyTest extends TestCase
{
	public function testCanIfHasPermission()
	{
		$user = User::factory()->create();
		$user->group->forum_post_manage = true;
		$user->push();

		$post = Post::factory()->create();

		$this->assertTrue($user->can('fix', $post));
	}

	public function testCantIfDoesntHavePermission()
	{
		$user = User::factory()->create();
		$user->group->forum_post_manage = false;
		$user->push();

		$post = Post::factory()->create();

		$this->assertFalse($user->can('fix', $post));
	}

	public function testCantIfChild()
	{
		$user = User::factory()->create();
		$user->group->forum_post_manage = true;
		$user->push();

		$parent = Post::factory()->create();

		$post = Post::factory()->create(['parent' => $parent]);

		$this->assertFalse($user->can('fix', $post));
	}

	public function testCantIfFixed()
	{
		$user = User::factory()->create();
		$user->group->forum_post_manage = true;
		$user->push();

		$post = Post::factory()->create();
		$post->fix();
		$post->refresh();

		$this->assertFalse($user->can('fix', $post));
	}
}
