<?php

namespace Tests\Feature\Job;

use App\Book;
use App\BookVote;
use App\Jobs\Author\UpdateAuthorRating;
use Tests\TestCase;

class UpdateAuthorRatingTest extends TestCase
{
    public function testRateCountUpdated()
    {
        $rating = BookVote::factory()
            ->for(Book::factory()->with_writer())
            ->create(['vote' => '8']);

        $author = $rating->book->writers->first();

        UpdateAuthorRating::dispatch($author);

        $this->assertEquals(1, $author->votes_count);

        $rating->delete();

        UpdateAuthorRating::dispatch($author);

        $author->refresh();

        $this->assertEquals(0, $author->votes_count);
    }
}
