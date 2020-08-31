<?php

namespace Tests\Feature\Book\Rating;

use App\BookVote;
use Carbon\Carbon;
use Tests\TestCase;

class BookRatingDateUpdateTest extends TestCase
{
	/**
	 * A basic test example.
	 *
	 * @return void
	 */
	public function testUpdateIsOk()
	{
		$book_vote = factory(BookVote::class)
			->create();

		$user = $book_vote->create_user;
		$book = $book_vote->book;

		$newDate = Carbon::createFromTimestamp(rand(100, time()));

		$response = $this->actingAs($user)
			->patch(route('books.ratings.date.update', ['book' => $book]), [
				'year' => $newDate->year,
				'month' => $newDate->month,
				'day' => $newDate->day,
				'hour' => $newDate->hour,
				'minute' => $newDate->minute,
				'second' => $newDate->second
			])
			->assertOk()
			->assertViewIs('book.date_of_rating')
			->assertViewHas('book', $book)
			->assertViewHas('user_rating', $book_vote);
	}

	/**
	 * A basic test example.
	 *
	 * @return void
	 */
	public function testUpdateIfWrongDate()
	{
		$book_vote = factory(BookVote::class)
			->create();

		$user = $book_vote->create_user;
		$book = $book_vote->book;

		$response = $this->actingAs($user)
			->ajax()
			->acceptJson()
			->patch(route('books.ratings.date.update', ['book' => $book]), [
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
