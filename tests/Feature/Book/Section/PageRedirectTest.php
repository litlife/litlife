<?php

namespace Tests\Feature\Book\Section;

use App\Book;
use App\Jobs\Book\BookUpdatePageNumbersJob;
use App\Section;
use Tests\TestCase;

class PageRedirectTest extends TestCase
{
	public function testIsRedirectOk()
	{
		$book = Book::factory()->create();

		$chapter1 = Section::factory()->with_two_pages()->create();

		$chapter2 = Section::factory()->with_two_pages()->create();

		BookUpdatePageNumbersJob::dispatch($book);

		$this->get(route('books.pages', ['book' => $book, 'page' => 2]))
			->assertRedirect(route('books.sections.show', [
				'book' => $book,
				'section' => $chapter1->inner_id,
				'page' => 2
			]));

		$this->get(route('books.pages', ['book' => $book, 'page' => 4]))
			->assertRedirect(route('books.sections.show', [
				'book' => $book,
				'section' => $chapter2->inner_id,
				'page' => 2
			]));
	}

	public function testPageNotFound()
	{
		$book = Book::factory()->create();

		$chapter1 = Section::factory()->with_two_pages()->create();

		BookUpdatePageNumbersJob::dispatch($book);

		$this->get(route('books.pages', ['book' => $book, 'page' => 60]))
			->assertNotFound();
	}

	public function testPageGreaterThanSmallIntIsNotFound()
	{
		$book = Book::factory()->create();

		$this->get(route('books.pages', ['book' => $book, 'page' => 99999999999]))
			->assertNotFound();
	}

	public function testIfSectionDeleted()
	{
		$book = Book::factory()->create();

		$chapter = Section::factory()->with_two_pages()->create();

		BookUpdatePageNumbersJob::dispatch($book);

		$page = $chapter->pages()->first();

		$this->assertNotNull($page);

		$chapter->delete();

		$this->get(route('books.pages', ['book' => $book, 'page' => $page->page]))
			->assertNotFound();
	}
}
