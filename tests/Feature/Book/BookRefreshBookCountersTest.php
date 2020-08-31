<?php

namespace Tests\Feature\Book;

use App\Book;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class BookRefreshBookCountersTest extends TestCase
{
	public function test()
	{
		$updated_at = now();

		$book = factory(Book::class)
			->create(['updated_at' => $updated_at]);

		Artisan::call('refresh:book_counters', ['id' => $book->id]);

		$book->refresh();

		$this->assertGreaterThanOrEqual($updated_at->timestamp, $book->updated_at->timestamp);
	}
}
