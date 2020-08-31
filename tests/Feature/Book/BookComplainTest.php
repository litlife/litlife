<?php

namespace Tests\Feature\Book;

use App\Book;
use App\User;
use Tests\TestCase;

class BookComplainTest extends TestCase
{
	public function testCantComplainForPrivateBook()
	{
		$user = factory(User::class)->create();
		$user->group->complain = true;
		$user->push();
		$user->refresh();

		$book = factory(Book::class)
			->states('private')
			->create();

		$this->assertFalse($user->can('complain', $book));
	}

	public function testCantComplainForAcceptedBook()
	{
		$user = factory(User::class)->create();
		$user->group->complain = true;
		$user->push();
		$user->refresh();

		$book = factory(Book::class)
			->states('accepted')
			->create();

		$this->assertTrue($user->can('complain', $book));
	}
}
