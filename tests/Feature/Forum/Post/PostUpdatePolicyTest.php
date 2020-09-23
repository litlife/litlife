<?php

namespace Tests\Feature\Forum\Post;

use App\Post;
use App\User;
use Tests\TestCase;

class PostUpdatePolicyTest extends TestCase
{
	public function testCanEditSelfPostIfUserCreatorAndHasPermissions()
	{
		$user = factory(User::class)->states('with_user_group')->create();
		$user->group->forum_edit_self_post = true;
		$user->push();

		$post = factory(Post::class)->create();
		$post->create_user()->associate($user);
		$post->push();

		$this->assertTrue($user->can('update', $post));
	}

	public function testCantEditSelfPostIfNoPermissions()
	{
		$user = factory(User::class)->states('with_user_group')->create();
		$user->group->forum_edit_self_post = false;
		$user->push();

		$post = factory(Post::class)->create();
		$post->create_user()->associate($user);
		$post->push();

		$this->assertFalse($user->can('update', $post));
	}

	public function testCantEditOtherUserIfNoPermissions()
	{
		$user = factory(User::class)->states('with_user_group')->create();
		$user->group->forum_edit_other_user_post = false;
		$user->push();

		$post = factory(Post::class)
			->create();

		$this->assertFalse($user->can('update', $post));
	}

	public function testCanEditOtherUserIfHasPermissions()
	{
		$user = factory(User::class)->states('with_user_group')->create();
		$user->group->forum_edit_other_user_post = true;
		$user->push();

		$post = factory(Post::class)
			->create();

		$this->assertTrue($user->can('update', $post));
	}

	public function testEditOnlyTimePolicy()
	{
		$user = factory(User::class)->states('with_user_group')->create();
		$user->group->forum_edit_self_post = false;
		$user->push();

		$post = factory(Post::class)->create();
		$post->create_user()->associate($user);
		$post->push();

		$this->assertFalse($user->can('update', $post));

		$user->group->forum_edit_self_post_only_time = true;
		$user->push();

		$this->assertTrue($user->can('update', $post));

		$post->created_at = now()->subMonths(1);
		$post->push();

		$this->assertFalse($user->can('update', $post));
	}
}
