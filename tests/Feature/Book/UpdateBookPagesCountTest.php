<?php

namespace Tests\Feature\Book;

use App\Book;
use App\Jobs\Book\UpdateBookPagesCount;
use App\Section;
use Tests\TestCase;

class UpdateBookPagesCountTest extends TestCase
{
	public function testAcceptedChapter()
	{
		$book = factory(Book::class)->create();

		$section = factory(Section::class)
			->states('accepted')
			->create(['book_id' => $book->id]);

		UpdateBookPagesCount::dispatch($book);

		$book->refresh();

		$this->assertEquals(2, $book->page_count);
	}

	public function testPrivateChapter()
	{
		$book = factory(Book::class)->create();

		$section = factory(Section::class)
			->states('private')
			->create(['book_id' => $book->id]);

		UpdateBookPagesCount::dispatch($book);

		$book->refresh();

		$this->assertEquals(0, $book->page_count);
	}
}
