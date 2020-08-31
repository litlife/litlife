<?php

namespace Tests\Feature\Artisan;

use App\Book;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class RefreshAllBooksCountersTest extends TestCase
{
	public function testCommand()
	{
		$book = factory(Book::class)->create();
		$book->updated_at = now()->subYear();
		$book->save(['timestamps' => false]);

		Artisan::call('refresh:all_books_counters', ['latest_id' => $book->id]);

		$this->assertNotEquals(
			$book->updated_at->timestamp,
			$book->fresh()->updated_at->timestamp
		);
	}
}
