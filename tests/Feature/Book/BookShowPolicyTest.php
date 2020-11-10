<?php

namespace Tests\Feature\Book;

use App\Book;
use App\User;
use Tests\TestCase;

class BookShowPolicyTest extends TestCase
{
	public function testCanViewIfAccepted()
	{
		$book = Book::factory()->accepted()->with_create_user()->create();

		$user = User::factory()->create();

		$this->assertTrue($user->can('view', $book));
	}

	public function testCanViewIfBookPrivateAndUserCreator()
	{
		$book = Book::factory()->private()->with_create_user()->create();

		$user = $book->create_user;

		$this->assertTrue($user->can('view', $book));
	}

	public function testCantViewIfBookPrivateAndUserNotCreator()
	{
		$book = Book::factory()->private()->with_create_user()->create();

		$user = User::factory()->create();

		$this->assertFalse($user->can('view', $book));
	}
}
