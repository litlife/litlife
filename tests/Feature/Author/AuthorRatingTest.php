<?php

namespace Tests\Feature\Author;

use App\Author;
use App\BookVote;
use App\Jobs\Author\UpdateAuthorRating;
use App\Jobs\Book\UpdateBookRating;
use Tests\TestCase;

class AuthorRatingTest extends TestCase
{
	public function testUpdate()
	{
		$author = factory(Author::class)->create();

		$vote = factory(BookVote::class)->create(['vote' => 6]);
		$vote2 = factory(BookVote::class)->create(['vote' => 4]);

		$book = $vote->book;
		$book2 = $vote2->book;

		$author->written_books()->sync([$book->id]);
		$author->compiled_books()->sync([$book2->id]);

		UpdateBookRating::dispatch($book);
		UpdateBookRating::dispatch($book2);
		UpdateAuthorRating::dispatch($author);

		$author->refresh();

		$this->assertFalse($author->rating_changed);

		$this->assertEquals(2, $author->votes_count);
		$this->assertEquals(5, $author->vote_average);

		$this->assertEquals(10, $author->rating);

		$this->assertEquals(10, $author->averageRatingForPeriod->day_rating);
		$this->assertEquals(10, $author->averageRatingForPeriod->week_rating);
		$this->assertEquals(10, $author->averageRatingForPeriod->month_rating);
		$this->assertEquals(10, $author->averageRatingForPeriod->quarter_rating);
		$this->assertEquals(10, $author->averageRatingForPeriod->year_rating);
		$this->assertEquals(10, $author->averageRatingForPeriod->all_rating);
	}
}
