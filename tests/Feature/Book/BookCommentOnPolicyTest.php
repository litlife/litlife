<?php

namespace Tests\Feature\Book;

use App\Book;
use App\User;
use Tests\TestCase;

class BookCommentOnPolicyTest extends TestCase
{
	public function testTrueIfUserCreatorOfPrivateBook()
	{
		$user = User::factory()->create();
		$user->group->add_comment = true;
		$user->push();

		$book = Book::factory()->private()->create();

		$this->assertTrue($user->can('commentOn', $book));
	}

	public function testFalseIfUserNotCreatorOfPrivateBook()
	{
		$user = User::factory()->create();
		$user->group->add_comment = true;
		$user->push();

		$book = Book::factory()->private()->create(
			)
			->fresh();

		$this->assertFalse($user->can('commentOn', $book));
	}

	public function testTrueIfCommentsNotClosed()
	{
		$user = User::factory()->create();
		$user->group->add_comment = true;
		$user->push();

		$book = Book::factory()->create(['create_user_id' => $user->id]);

		$this->assertTrue($user->can('commentOn', $book));
	}

	public function testFalseIfCommentsClosed()
	{
		$user = User::factory()->create();
		$user->group->add_comment = true;
		$user->push();

		$book = Book::factory()->create(['create_user_id' => $user->id]);
		$book->comments_closed = true;
		$book->push();

		$this->assertFalse($user->can('commentOn', $book));
	}
}
