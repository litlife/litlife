<?php

namespace Tests\Feature\Book;

use App\Book;
use Carbon\Carbon;
use Tests\TestCase;

class BookGetDiffBeetweenLastPriceChangeInDaysTest extends TestCase
{
	public function testDiffBeetweenLastPriceChangeInDays()
	{
		$book = Book::factory()->create();

		$this->assertEquals(0, $book->getDiffBeetweenLastPriceChangeInDays());

		$book->price_updated_at = now();
		$book->save();
		$book->refresh();

		$now = now();

		config(['litlife.book_price_update_cooldown' => 14]);

		Carbon::setTestNow($now->copy()->addMinute());

		$this->assertEquals(14, $book->getDiffBeetweenLastPriceChangeInDays());

		Carbon::setTestNow($now->copy()->addHour());

		$this->assertEquals(14, $book->getDiffBeetweenLastPriceChangeInDays());

		Carbon::setTestNow($now->copy()->addDays(7)->addHour());

		$this->assertEquals(7, $book->getDiffBeetweenLastPriceChangeInDays());

		Carbon::setTestNow($now->copy()->addDays(13)->addHour());

		$this->assertEquals(1, $book->getDiffBeetweenLastPriceChangeInDays());

		Carbon::setTestNow($now->copy()->addDays(14)->addHour());

		$this->assertEquals(0, $book->getDiffBeetweenLastPriceChangeInDays());

		Carbon::setTestNow($now->copy()->addDays(16));

		$this->assertEquals(0, $book->getDiffBeetweenLastPriceChangeInDays());
	}
}
