<?php

namespace Tests\Feature\Book;

use App\Book;
use App\User;
use Tests\TestCase;

class BookShowPolicyTest extends TestCase
{
	public function testCanViewIfAccepted()
	{
		$book = factory(Book::class)
			->states('accepted', 'with_create_user')
			->create();

		$user = factory(User::class)->create();

		$this->assertTrue($user->can('view', $book));
	}

	public function testCanViewIfBookPrivateAndUserCreator()
	{
		$book = factory(Book::class)
			->states('private', 'with_create_user')
			->create();

		$user = $book->create_user;

		$this->assertTrue($user->can('view', $book));
	}

	public function testCantViewIfBookPrivateAndUserNotCreator()
	{
		$book = factory(Book::class)
			->states('private', 'with_create_user')
			->create();

		$user = factory(User::class)->create();

		$this->assertFalse($user->can('view', $book));
	}
}
