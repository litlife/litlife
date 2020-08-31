<?php

namespace Tests\Feature\Comment;

use App\Comment;
use Tests\TestCase;

class CommentDeletePolicyTest extends TestCase
{
	public function testAlwaysCanUpdateIfCommentPrivate()
	{
		$comment = factory(Comment::class)
			->states('private')
			->create();

		$user = $comment->create_user;

		$this->assertTrue($user->can('delete', $comment));
	}
}
