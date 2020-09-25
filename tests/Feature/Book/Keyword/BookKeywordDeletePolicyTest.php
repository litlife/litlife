<?php

namespace Tests\Feature\Book\Keyword;

use App\Book;
use App\BookKeyword;
use App\User;
use Tests\TestCase;

class BookKeywordDeletePolicyTest extends TestCase
{
	public function testCantIfNoPermission()
	{
		$user = factory(User::class)
			->create();

		$book_keyword = factory(BookKeyword::class)
			->create();

		$this->assertFalse($user->can('delete', $book_keyword));
	}

	public function testCanIfHasPermission()
	{
		$user = factory(User::class)->create();
		$user->group->book_keyword_remove = true;
		$user->push();

		$book_keyword = factory(BookKeyword::class)
			->create();

		$this->assertTrue($user->can('delete', $book_keyword));
	}

	public function testCanIfBookPrivate()
	{
		$user = factory(User::class)
			->create();

		$book = factory(Book::class)
			->states('private')
			->create(['create_user_id' => $user->id]);

		$book_keyword = factory(BookKeyword::class)
			->states('private')
			->make(['create_user_id' => $user->id]);

		$book->book_keywords()->save($book_keyword);

		$this->assertTrue($user->can('delete', $book_keyword));
	}

	public function testCanDeleteIfOnReviewAndUserCreator()
	{
		$book_keyword = factory(BookKeyword::class)
			->states('on_review')->create();

		$user = $book_keyword->create_user;

		$this->assertTrue($user->can('delete', $book_keyword));
	}

	public function testCantDeleteIfAcceptedAndUserCreator()
	{
		$book_keyword = factory(BookKeyword::class)
			->states('accepted')->create();

		$user = $book_keyword->create_user;

		$this->assertFalse($user->can('delete', $book_keyword));
	}
}
