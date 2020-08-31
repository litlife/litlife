<?php

namespace Tests\Feature\Book\Rating;

use App\BookVote;
use Tests\TestCase;

class BookRatingDateEditTest extends TestCase
{
	/**
	 * A basic test example.
	 *
	 * @return void
	 */
	public function testEditHttpIsOk()
	{
		$book_vote = factory(BookVote::class)->create();

		$user = $book_vote->create_user;
		$book = $book_vote->book;

		$this->actingAs($user)
			->get(route('books.ratings.date.edit', ['book' => $book]))
			->assertOk()
			->assertViewIs('book.edit_date_of_rating')
			->assertViewHas('book', $book)
			->assertViewHas('user_rating', $book_vote)
			->assertViewHas('user_updated_at', $book_vote->user_updated_at->timezone(session()->get('geoip')->timezone));
	}
}
