<?php

namespace Tests\Feature\Forum\Post;

use App\Post;
use App\User;
use Tests\TestCase;

class PostDeletePolicyTest extends TestCase
{
	public function testCantDeleteFixedPost()
	{
		$post = factory(Post::class)->create();
		$post->fix();

		$user = factory(User::class)
			->states('admin')
			->create();

		$this->assertFalse($user->can('delete', $post));
	}

	public function testCanDeleteCreatedPostHasPerimission()
	{
		$post = factory(Post::class)->create();

		$user = $post->create_user;
		$user->group->forum_delete_self_post = true;
		$user->push();

		$this->assertTrue($user->can('delete', $post));
	}

	public function testCantDeleteCreatedPostIfDoesntHavePerimission()
	{
		$post = factory(Post::class)->create();

		$user = $post->create_user;
		$user->group->forum_delete_self_post = false;
		$user->push();

		$this->assertFalse($user->can('delete', $post));
	}

	public function testCanDeleteOtherUserPostIfHasPerimission()
	{
		$post = factory(Post::class)->create();

		$user = factory(User::class)->create();
		$user->group->forum_delete_other_user_post = true;
		$user->push();

		$this->assertTrue($user->can('delete', $post));
	}

	public function testCantDeleteOtherUserPostIfDoesntHavePerimission()
	{
		$post = factory(Post::class)->create();

		$user = $post->create_user;
		$user->group->forum_delete_other_user_post = false;
		$user->push();

		$this->assertFalse($user->can('delete', $post));
	}

	public function testCantDeleteIfTrashed()
	{
		$post = factory(Post::class)->create();

		$post->delete();

		$user = factory(User::class)->states('admin')->create();

		$this->assertFalse($user->can('delete', $post));
	}

	public function testCantRestoreIfRestored()
	{
		$post = factory(Post::class)->create();

		$user = factory(User::class)->states('admin')->create();

		$this->assertFalse($user->can('restore', $post));
	}
}
