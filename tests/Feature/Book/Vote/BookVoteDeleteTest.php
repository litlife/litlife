<?php

namespace Tests\Feature\Book\Vote;

use App\Book;
use App\BookVote;
use App\User;
use Tests\TestCase;

class BookVoteDeleteTest extends TestCase
{
    public function testIfCreatorDeleted()
    {
        $rating = BookVote::factory()->create();

        $this->assertInstanceOf(User::class, $rating->create_user);

        $rating->create_user->forceDelete();
        $rating->refresh();
        $rating->delete();
        $rating->refresh();

        $this->assertTrue($rating->trashed());
    }

    public function testIfBookDeleted()
    {
        $rating = BookVote::factory()->create();

        $this->assertInstanceOf(Book::class, $rating->book);

        $rating->book->forceDelete();
        $rating->refresh();
        $rating->delete();
        $rating->refresh();

        $this->assertTrue($rating->trashed());
    }
}
