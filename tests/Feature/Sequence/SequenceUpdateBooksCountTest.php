<?php

namespace Tests\Feature\Sequence;

use App\Book;
use App\Jobs\Sequence\UpdateSequenceBooksCount;
use App\Sequence;
use App\User;
use Tests\TestCase;

class SequenceUpdateBooksCountTest extends TestCase
{
    public function testBooksCountIfPrivate()
    {
        $user = User::factory()->create();

        $sequence = Sequence::factory()
            ->private()
            ->create(['create_user_id' => $user->id]);

        $book = Book::factory()
            ->private()
            ->create(['create_user_id' => $user->id]);

        $book2 = Book::factory()
            ->private()
            ->create(['create_user_id' => $user->id]);

        $sequence->books()->sync([$book->id, $book2->id]);

        UpdateSequenceBooksCount::dispatch($sequence);

        $sequence->refresh();

        $this->assertEquals(2, $sequence->book_count);
    }
}
