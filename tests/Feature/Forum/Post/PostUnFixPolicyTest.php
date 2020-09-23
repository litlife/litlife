<?php

namespace Tests\Feature\Forum\Post;

use App\Post;
use App\User;
use Tests\TestCase;

class PostUnFixPolicyTest extends TestCase
{
	public function testCanIfHasPermission()
	{
		$user = factory(User::class)->create();
		$user->group->forum_post_manage = true;
		$user->push();

		$post = factory(Post::class)
			->states('fixed')
			->create();

		$this->assertTrue($user->can('unfix', $post));
	}

	public function testCantIfDoesntHavePermission()
	{
		$user = factory(User::class)->create();
		$user->group->forum_post_manage = false;
		$user->push();

		$post = factory(Post::class)
			->states('fixed')
			->create();

		$this->assertFalse($user->can('unfix', $post));
	}

	public function testCantIfUnFixed()
	{
		$user = factory(User::class)->create();
		$user->group->forum_post_manage = true;
		$user->push();

		$post = factory(Post::class)->create();

		$this->assertFalse($user->can('unfix', $post));
	}
}
