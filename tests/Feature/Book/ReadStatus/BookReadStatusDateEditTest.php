<?php

namespace Tests\Feature\Book\ReadStatus;

use App\BookStatus;
use Tests\TestCase;

class BookReadStatusDateEditTest extends TestCase
{
	/**
	 * A basic test example.
	 *
	 * @return void
	 */
	public function testEditHttpIsOk()
	{
		$user_read_status = factory(BookStatus::class)->create();

		$user = $user_read_status->user;
		$book = $user_read_status->book;

		$this->actingAs($user)
			->get(route('books.read_status.date.edit', ['book' => $book]))
			->assertOk()
			->assertViewIs('book.read_status.date.edit')
			->assertViewHas('book', $book)
			->assertViewHas('user_read_status', $user_read_status)
			->assertViewHas('user_updated_at', $user_read_status->user_updated_at->timezone(session()->get('geoip')->timezone));
	}

	/**
	 * A basic test example.
	 *
	 * @return void
	 */
	public function testEditHttpIfUserUpdatedAtIsNull()
	{
		$user_read_status = factory(BookStatus::class)
			->create(['user_updated_at' => null]);

		$user = $user_read_status->user;
		$book = $user_read_status->book;

		$this->assertNull($user_read_status->user_updated_at);

		$this->actingAs($user)
			->get(route('books.read_status.date.edit', ['book' => $book]))
			->assertOk()
			->assertViewIs('book.read_status.date.edit')
			->assertViewHas('book', $book)
			->assertViewHas('user_read_status', $user_read_status);
	}
}
