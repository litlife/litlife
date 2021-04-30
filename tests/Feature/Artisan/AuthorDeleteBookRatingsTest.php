<?php

namespace Tests\Feature\Artisan;

use App\Book;
use App\BookVote;
use App\Jobs\Author\UpdateAuthorRating;
use App\Jobs\Book\UpdateBookRating;
use App\Jobs\User\UpdateUserBookVotesCount;
use Carbon\Carbon;
use Illuminate\Support\Facades\Bus;
use Tests\TestCase;

class AuthorDeleteBookRatingsTest extends TestCase
{
    public function testDeleteAfterTime()
    {
        Bus::fake([UpdateBookRating::class, UpdateAuthorRating::class, UpdateUserBookVotesCount::class]);

        $time = Carbon::now();

        $rating = BookVote::factory()
            ->for(Book::factory()->with_writer())
            ->create(['user_updated_at' => $time]);

        $author = $rating->book->writers->first();

        $this->assertNotNull($author);

        $this->artisan('author:delete_book_ratings', ['author_id' => $author->id, 'older_than' => $time])
            ->assertExitCode(0);

        $rating->refresh();

        $this->assertNotNull($rating->deleted_at);

        Bus::assertDispatched(UpdateBookRating::class);
        Bus::assertDispatched(UpdateAuthorRating::class);
        Bus::assertDispatched(UpdateUserBookVotesCount::class);
    }

    public function testDontDeleteDeleteBeforeTime()
    {
        $time = Carbon::now();

        $rating = BookVote::factory()
            ->for(Book::factory()->with_writer())
            ->create(['user_updated_at' => $time]);

        $author = $rating->book->writers->first();

        $this->assertNotNull($author);

        $this->artisan('author:delete_book_ratings', ['author_id' => $author->id, 'older_than' => $time->addMinute()])
            ->assertExitCode(0);

        $rating->refresh();

        $this->assertNull($rating->deleted_at);
    }

    public function testDeleteAllIfTimeNotSet()
    {
        $rating = BookVote::factory()
            ->for(Book::factory()->with_writer())
            ->create();

        $author = $rating->book->writers->first();

        $this->assertNotNull($author);

        $this->artisan('author:delete_book_ratings', ['author_id' => $author->id])
            ->assertExitCode(0);

        $rating->refresh();

        $this->assertNotNull($rating->deleted_at);
    }
}
