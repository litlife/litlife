<?php

namespace Tests\Feature\Book;

use App\Book;
use App\User;
use Tests\TestCase;

class BookCommentOnPolicyTest extends TestCase
{
	public function testTrueIfUserCreatorOfPrivateBook()
	{
		$user = factory(User::class)->create();
		$user->group->add_comment = true;
		$user->push();

		$book = factory(Book::class)
			->states('private')
			->create(['create_user_id' => $user->id]);

		$this->assertTrue($user->can('commentOn', $book));
	}

	public function testFalseIfUserNotCreatorOfPrivateBook()
	{
		$user = factory(User::class)->create();
		$user->group->add_comment = true;
		$user->push();

		$book = factory(Book::class)
			->states('private')
			->create()
			->fresh();

		$this->assertFalse($user->can('commentOn', $book));
	}

	public function testTrueIfCommentsNotClosed()
	{
		$user = factory(User::class)->create();
		$user->group->add_comment = true;
		$user->push();

		$book = factory(Book::class)
			->create(['create_user_id' => $user->id]);

		$this->assertTrue($user->can('commentOn', $book));
	}

	public function testFalseIfCommentsClosed()
	{
		$user = factory(User::class)->create();
		$user->group->add_comment = true;
		$user->push();

		$book = factory(Book::class)
			->create(['create_user_id' => $user->id]);
		$book->comments_closed = true;
		$book->push();

		$this->assertFalse($user->can('commentOn', $book));
	}
}
