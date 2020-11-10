<?php

namespace Tests\Feature\Book\BookGroup;

use App\Book;
use Tests\TestCase;

class BookUpdateEditionsCountTest extends TestCase
{
	public function testUpdateMainBookEditionsCount()
	{
		$mainBook = Book::factory()->with_minor_book()->create();

		$minorBook = $mainBook->groupedBooks()->first();

		$mainBook->editions_count = null;
		$mainBook->save();

		$minorBook->editions_count = null;
		$minorBook->save();

		$mainBook->updateEditionsCount();

		$mainBook->refresh();
		$minorBook->refresh();

		$this->assertEquals(1, $mainBook->editions_count);
		$this->assertEquals(1, $minorBook->editions_count);
	}

	public function testUpdateMinorBookEditionsCount()
	{
		$mainBook = Book::factory()->with_minor_book()->create();

		$minorBook = $mainBook->groupedBooks()->first();

		$mainBook->editions_count = null;
		$mainBook->save();

		$minorBook->editions_count = null;
		$minorBook->save();

		$minorBook->updateEditionsCount();

		$mainBook->refresh();
		$minorBook->refresh();

		$this->assertEquals(1, $mainBook->editions_count);
		$this->assertEquals(1, $minorBook->editions_count);
	}
}
