<?php

namespace Tests\Feature\Book\Collection;

use App\Book;
use App\User;
use Tests\TestCase;

class BookCollectionAddToCollectionPolicyTest extends TestCase
{
	public function testCanIfBookAccepted()
	{
		$book = factory(Book::class)
			->states('accepted')
			->create();

		$user = factory(User::class)
			->create();

		$this->assertTrue($user->can('addToCollection', $book));
	}

	public function testCantIfBookPrivate()
	{
		$book = factory(Book::class)
			->states('private')
			->create();

		$user = factory(User::class)
			->create();

		$this->assertFalse($user->can('addToCollection', $book));
	}
}
