<?php

namespace Tests\Feature\Book\Keyword;

use App\Book;
use Tests\TestCase;

class BookKeywordCreatePolicyTest extends TestCase
{
	public function testCanIfBookPrivate()
	{
		$book = factory(Book::class)
			->states('private', 'with_create_user')
			->create();

		$user = $book->create_user;

		$this->assertTrue($user->can('addKeywords', $book));
	}

	public function testCantIfNoPermission()
	{
		$book = factory(Book::class)
			->states('accepted', 'with_create_user')
			->create();

		$user = $book->create_user;

		$this->assertFalse($user->can('addKeywords', $book));
	}

	public function testCanIfHasPermission1()
	{
		$book = factory(Book::class)
			->states('accepted', 'with_create_user')
			->create();

		$user = $book->create_user;
		$user->group->book_keyword_add = true;
		$user->save();

		$this->assertTrue($user->can('addKeywords', $book));
	}

	public function testCanIfHasPermission2()
	{
		$book = factory(Book::class)
			->states('accepted', 'with_create_user')
			->create();

		$user = $book->create_user;
		$user->group->book_keyword_add_new_with_check = true;
		$user->save();

		$this->assertTrue($user->can('addKeywords', $book));
	}

	public function testCanIfHasPermission3()
	{
		$book = factory(Book::class)
			->states('accepted', 'with_create_user')
			->create();

		$user = $book->create_user;
		$user->group->book_keyword_moderate = true;
		$user->save();

		$this->assertTrue($user->can('addKeywords', $book));
	}
}
