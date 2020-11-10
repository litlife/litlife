<?php

namespace Tests\Feature\Job;

use App\Book;
use App\Jobs\Book\UpdateBookPagesCount;
use App\Section;
use Tests\TestCase;

class UpdateBookPagesCountTest extends TestCase
{
	public function testAcceptedChapter()
	{
		$book = Book::factory()->create();

		$section = Section::factory()->accepted()->create();

		UpdateBookPagesCount::dispatch($book);

		$book->refresh();

		$this->assertEquals(2, $book->page_count);
	}

	public function testPrivateChapter()
	{
		$book = Book::factory()->create();

		$section = Section::factory()->private()->create();

		UpdateBookPagesCount::dispatch($book);

		$book->refresh();

		$this->assertEquals(0, $book->page_count);
	}
}
