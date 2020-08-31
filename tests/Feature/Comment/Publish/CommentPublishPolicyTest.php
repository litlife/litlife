<?php

namespace Tests\Feature\Comment\Publish;

use App\Comment;
use App\User;
use Tests\TestCase;

class CommentPublishPolicyTest extends TestCase
{
	public function testCanPublishPrivate()
	{
		$comment = factory(Comment::class)
			->states('private')
			->create();

		$user = $comment->create_user;

		$this->assertTrue($user->can('publish', $comment));
	}

	public function testCantPublishNotPrivate()
	{
		$comment = factory(Comment::class)
			->states('accepted')
			->create();

		$user = $comment->create_user;

		$this->assertFalse($user->can('publish', $comment));
	}

	public function testCantPublishByOtherUser()
	{
		$comment = factory(Comment::class)
			->states('private')
			->create();

		$user = factory(User::class)
			->states('admin')
			->create();

		$this->assertFalse($user->can('publish', $comment));
	}

	public function testCantPublishTrashed()
	{
		$comment = factory(Comment::class)
			->states('private')
			->create();

		$user = $comment->create_user;

		$comment->delete();

		$this->assertFalse($user->can('publish', $comment));
	}
}
