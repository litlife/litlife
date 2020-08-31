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
		$book = factory(Book::class)
			->create();

		$chapter1 = factory(Section::class)
			->states('with_two_pages')
			->create(['book_id' => $book->id]);

		$chapter2 = factory(Section::class)
			->states('with_two_pages')
			->create(['book_id' => $book->id]);

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
		$book = factory(Book::class)
			->create();

		$chapter1 = factory(Section::class)
			->states('with_two_pages')
			->create(['book_id' => $book->id]);

		BookUpdatePageNumbersJob::dispatch($book);

		$this->get(route('books.pages', ['book' => $book, 'page' => 60]))
			->assertNotFound();
	}

	public function testPageGreaterThanSmallIntIsNotFound()
	{
		$book = factory(Book::class)
			->create();

		$this->get(route('books.pages', ['book' => $book, 'page' => 99999999999]))
			->assertNotFound();
	}

	public function testIfSectionDeleted()
	{
		$book = factory(Book::class)
			->create();

		$chapter = factory(Section::class)
			->states('with_two_pages')
			->create(['book_id' => $book->id]);

		BookUpdatePageNumbersJob::dispatch($book);

		$page = $chapter->pages()->first();

		$this->assertNotNull($page);

		$chapter->delete();

		$this->get(route('books.pages', ['book' => $book, 'page' => $page->page]))
			->assertNotFound();
	}
}
