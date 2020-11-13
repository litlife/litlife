<?php

namespace Tests\Feature\Job;

use App\Book;
use App\Jobs\Sequence\UpdateSequenceBooksCount;
use App\Sequence;
use App\User;
use Tests\TestCase;

class UpdateSequenceBooksCountTest extends TestCase
{
    public function testCountAcceptedBooksIfSequenceAccepted()
    {
        $sequence = Sequence::factory()->accepted()->create();

        $book = Book::factory()->accepted()->create();

        $sequence->books()->sync($book->id);

        UpdateSequenceBooksCount::dispatch($sequence);

        $sequence->refresh();

        $this->assertEquals(1, $sequence->book_count);
    }

    public function testCountPrivateIfBookPrivate()
    {
        $user = User::factory()->create();

        $sequence = Sequence::factory()->private()->create(['create_user_id' => $user->id]);

        $book = Book::factory()->private()->create(['create_user_id' => $user->id]);

        $sequence->books()->sync($book->id);

        UpdateSequenceBooksCount::dispatch($sequence);

        $sequence->refresh();

        $this->assertEquals(1, $sequence->book_count);
    }
}
