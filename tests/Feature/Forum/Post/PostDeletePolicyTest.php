<?php

namespace Tests\Feature\Forum\Post;

use App\Post;
use App\User;
use Tests\TestCase;

class PostDeletePolicyTest extends TestCase
{
	public function testCantDeleteFixedPost()
	{
		$post = Post::factory()->create();
		$post->fix();

		$user = User::factory()->admin()->create();

		$this->assertFalse($user->can('delete', $post));
	}

	public function testCanDeleteCreatedPostHasPerimission()
	{
		$post = Post::factory()->create();

		$user = $post->create_user;
		$user->group->forum_delete_self_post = true;
		$user->push();

		$this->assertTrue($user->can('delete', $post));
	}

	public function testCantDeleteCreatedPostIfDoesntHavePerimission()
	{
		$post = Post::factory()->create();

		$user = $post->create_user;
		$user->group->forum_delete_self_post = false;
		$user->push();

		$this->assertFalse($user->can('delete', $post));
	}

	public function testCanDeleteOtherUserPostIfHasPerimission()
	{
		$post = Post::factory()->create();

		$user = User::factory()->create();
		$user->group->forum_delete_other_user_post = true;
		$user->push();

		$this->assertTrue($user->can('delete', $post));
	}

	public function testCantDeleteOtherUserPostIfDoesntHavePerimission()
	{
		$post = Post::factory()->create();

		$user = $post->create_user;
		$user->group->forum_delete_other_user_post = false;
		$user->push();

		$this->assertFalse($user->can('delete', $post));
	}

	public function testCantDeleteIfTrashed()
	{
		$post = Post::factory()->create();

		$post->delete();

		$user = User::factory()->admin()->create();

		$this->assertFalse($user->can('delete', $post));
	}

	public function testCantRestoreIfRestored()
	{
		$post = Post::factory()->create();

		$user = User::factory()->admin()->create();

		$this->assertFalse($user->can('restore', $post));
	}
}
