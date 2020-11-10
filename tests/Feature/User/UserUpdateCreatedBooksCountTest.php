<?php

namespace Tests\Feature\User;

use App\Book;
use App\Jobs\User\UpdateUserCreatedBooksCount;
use Tests\TestCase;

class UserUpdateCreatedBooksCountTest extends TestCase
{
	public function testUpdateUserCreatedBooksOnCreate()
	{
		$book = Book::factory()->with_create_user()->create();

		$this->assertNotNull($book->create_user);

		$user = $book->create_user;

		UpdateUserCreatedBooksCount::dispatch($book->create_user);

		$user->refresh();

		$this->assertEquals(1, $user->data->created_books_count);

		$book->delete();
		$user->refresh();
		$book->refresh();

		$this->assertTrue($book->trashed());

		$this->assertEquals(0, $user->data->created_books_count);

		$book->restore();
		$user->refresh();
		$book->refresh();

		$this->assertFalse($book->trashed());

		$this->assertEquals(1, $user->data->created_books_count);
	}
}
