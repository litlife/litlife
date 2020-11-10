<?php

namespace Tests\Feature\Book\Section;

use App\Book;
use App\Jobs\Book\BookUpdatePageNumbersJob;
use Illuminate\Support\Facades\Bus;
use Tests\TestCase;

class SectionViewTest extends TestCase
{
	public function testIfBookPageNumberIsNull()
	{
		Bus::fake(BookUpdatePageNumbersJob::class);

		$book = Book::factory()->with_section()->create();

		//Bus::assertDispatched(BookUpdatePageNumbersJob::class);

		$section = $book->sections()->first();
		$page = $section->pages()->first();

		$this->assertNull($page->book_page);

		$this->get(route('books.sections.show', ['book' => $book, 'section' => $section->inner_id]))
			->assertOk()
			->assertViewHas('book_pages', null);
	}
}