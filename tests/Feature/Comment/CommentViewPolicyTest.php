<?php

namespace Tests\Feature\Comment;

use App\Comment;
use App\User;
use Illuminate\Support\Facades\Gate;
use Tests\TestCase;

class CommentViewPolicyTest extends TestCase
{
	public function testCanViewPrivateByCreator()
	{
		$comment = factory(Comment::class)
			->states('private')
			->create();

		$user = $comment->create_user;

		$this->assertTrue($user->can('view', $comment));
	}

	public function testCantViewPrivateIfOtherUser()
	{
		$comment = factory(Comment::class)
			->states('private')
			->create();

		$user = factory(User::class)
			->create();

		$this->assertFalse($user->can('view', $comment));
	}

	public function testCanViewAccepted()
	{
		$comment = factory(Comment::class)
			->states('accepted')
			->create();

		$user = factory(User::class)
			->create();

		$this->assertTrue($user->can('view', $comment));
	}

	public function testCanViewAcceptedByGuest()
	{
		$comment = factory(Comment::class)
			->states('accepted')
			->create();

		$this->assertTrue(Gate::forUser(new User)->allows('view', $comment));
		$this->assertTrue(Gate::allows('view', $comment));
	}

	public function testCanViewSentForReviewComment()
	{
		$comment = factory(Comment::class)
			->states('sent_for_review')
			->create();

		$user = factory(User::class)
			->create();

		$this->assertTrue($user->can('view', $comment));
	}
}
