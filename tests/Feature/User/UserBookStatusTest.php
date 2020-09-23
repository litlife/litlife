<?php

namespace Tests\Feature\User;

use App\Book;
use App\BookStatus;
use App\User;
use Tests\TestCase;

class UserBookStatusTest extends TestCase
{
	/**
	 * A basic test example.
	 *
	 * @return void
	 */
	public function testNew()
	{
		$book = factory(Book::class)
			->create();

		$user = factory(User::class)
			->create();

		$response = $this->actingAs($user)
			->get(route('books.read_status.store', ['book' => $book, 'code' => 'read_now']));
		$response->assertRedirect(route('books.show', $book));

		$read_status = $user->book_read_statuses()->first();
		$user->refresh();

		$this->assertNotNull($read_status);
		$this->assertNotNull($read_status->user_updated_at);
		$this->assertEquals(1, $user->book_read_now_count);
	}

	public function testUpdate()
	{
		$book_status = factory(BookStatus::class)
			->create(['status' => 'readed']);

		$user = $book_status->user;

		$response = $this->actingAs($user)
			->get(route('books.read_status.store',
				['book' => $book_status->book, 'code' => 'read_now']));

		$response->assertRedirect(route('books.show', $book_status->book));

		$new_book_status = $user->book_read_statuses()->first();

		$user->refresh();

		$this->assertEquals('read_now', $new_book_status->status);
		$this->assertNotNull($new_book_status->user_updated_at);
		$this->assertEquals(1, $user->book_read_now_count);
		$this->assertEquals(0, $user->book_readed_count);
		$this->assertGreaterThan($book_status->user_updated_at, $new_book_status->user_updated_at);
	}
}
