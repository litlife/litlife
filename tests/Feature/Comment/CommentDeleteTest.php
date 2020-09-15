<?php

namespace Tests\Feature\Comment;

use App\Comment;
use App\User;
use Tests\TestCase;

class CommentDeleteTest extends TestCase
{
	public function testIsOkIfBookSoftDeleted()
	{
		$user = factory(User::class)
			->states('admin')
			->create();

		$comment = factory(Comment::class)
			->create();

		$book = $comment->commentable;
		$book->delete();

		$this->actingAs($user)
			->delete(route('comments.destroy', $comment))
			->assertOk();

		$comment->refresh();

		$this->assertSoftDeleted($comment);
	}

	public function testIsOkIfBookForceDeleted()
	{
		$user = factory(User::class)
			->states('admin')
			->create();

		$comment = factory(Comment::class)
			->create();

		$book = $comment->commentable;
		$book->forceDelete();

		$this->actingAs($user)
			->delete(route('comments.destroy', $comment))
			->assertOk();

		$comment->refresh();

		$this->assertSoftDeleted($comment);
	}

	public function testIfCreatorDeleted()
	{
		$comment = factory(Comment::class)
			->states('book')
			->create();

		$comment->create_user->delete();
		$comment->refresh();
		$comment->delete();

		$this->assertTrue($comment->trashed());

		$comment = factory(Comment::class)
			->states('book')
			->create();

		$comment->create_user->forceDelete();
		$comment->refresh();
		$comment->delete();

		$this->assertTrue($comment->trashed());
	}
}
