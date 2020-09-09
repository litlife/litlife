<?php

namespace Tests\Feature\Sequence;

use App\Book;
use App\Jobs\Sequence\UpdateSequenceBooksCount;
use App\Sequence;
use App\User;
use Tests\TestCase;

class UpdateSequenceBooksCountTest extends TestCase
{
	public function testCountAcceptedBooksIfSequenceAccepted()
	{
		$sequence = factory(Sequence::class)
			->states('accepted')
			->create();

		$book = factory(Book::class)
			->states('accepted')
			->create();

		$sequence->books()->sync($book->id);

		UpdateSequenceBooksCount::dispatch($sequence);

		$sequence->refresh();

		$this->assertEquals(1, $sequence->book_count);
	}

	public function testCountPrivateIfBookPrivate()
	{
		$user = factory(User::class)
			->create();

		$sequence = factory(Sequence::class)
			->states('private')
			->create(['create_user_id' => $user->id]);

		$book = factory(Book::class)
			->states('private')
			->create(['create_user_id' => $user->id]);

		$sequence->books()->sync($book->id);

		UpdateSequenceBooksCount::dispatch($sequence);

		$sequence->refresh();

		$this->assertEquals(1, $sequence->book_count);
	}
}
