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
		$comment = Comment::factory()->private()->create();

		$user = $comment->create_user;

		$this->assertTrue($user->can('view', $comment));
	}

	public function testCantViewPrivateIfOtherUser()
	{
		$comment = Comment::factory()->private()->create();

		$user = User::factory()->create();

		$this->assertFalse($user->can('view', $comment));
	}

	public function testCanViewAccepted()
	{
		$comment = Comment::factory()->accepted()->create();

		$user = User::factory()->create();

		$this->assertTrue($user->can('view', $comment));
	}

	public function testCanViewAcceptedByGuest()
	{
		$comment = Comment::factory()->accepted()->create();

		$this->assertTrue(Gate::forUser(new User)->allows('view', $comment));
		$this->assertTrue(Gate::allows('view', $comment));
	}

	public function testCanViewSentForReviewComment()
	{
		$comment = Comment::factory()->sent_for_review()->create();

		$user = User::factory()->create();

		$this->assertTrue($user->can('view', $comment));
	}
}
