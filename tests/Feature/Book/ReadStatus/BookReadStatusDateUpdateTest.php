<?php

namespace Tests\Feature\Book\ReadStatus;

use App\BookStatus;
use Carbon\Carbon;
use Tests\TestCase;

class BookReadStatusDateUpdateTest extends TestCase
{
	/**
	 * A basic test example.
	 *
	 * @return void
	 */
	public function testUpdateIsOk()
	{
		$user_read_status = factory(BookStatus::class)
			->create();

		$user = $user_read_status->user;
		$book = $user_read_status->book;

		$newDate = Carbon::createFromTimestamp(rand(100, time()));

		$response = $this->actingAs($user)
			->patch(route('books.read_status.date.update', ['book' => $book]), [
				'year' => $newDate->year,
				'month' => $newDate->month,
				'day' => $newDate->day,
				'hour' => $newDate->hour,
				'minute' => $newDate->minute,
				'second' => $newDate->second
			])
			->assertOk()
			->assertViewIs('book.read_status.date.show')
			->assertViewHas('book', $book)
			->assertViewHas('user_read_status', $user_read_status);
	}

	/**
	 * A basic test example.
	 *
	 * @return void
	 */
	public function testUpdateIfWrongDate()
	{
		$user_read_status = factory(BookStatus::class)
			->create();

		$user = $user_read_status->user;
		$book = $user_read_status->book;

		$response = $this->actingAs($user)
			->ajax()
			->acceptJson()
			->patch(route('books.read_status.date.update', ['book' => $book]), [
				'year' => 2020,
				'month' => 2,
				'day' => 31,
				'hour' => 0,
				'minute' => 0,
				'second' => 0
			]);

		$response->assertStatus(422)
			->assertJsonFragment([
				'message' => 'The given data was invalid.',
				'errors' => [
					'year' => [
						__('Date is incorrect')
					]
				]
			]);
	}
}
