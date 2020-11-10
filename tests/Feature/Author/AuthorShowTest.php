<?php

namespace Tests\Feature\Author;

use App\Author;
use App\Book;
use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class AuthorShowTest extends TestCase
{
	public function testWithoutBooksHttp()
	{
		$author = Author::factory()->create();

		$this->get(route('authors.show', $author))
			->assertOk();
	}

	public function testView()
	{
		$author = Author::factory()->create();

		$book = Book::factory()->create();

		$translated_book = Book::factory()->create();

		$illustrated_book = Book::factory()->create();

		$author->books()->sync([$book->id]);
		$author->translated_books()->sync([$book->id]);
		$author->illustrated_books()->sync([$book->id]);

		$this->get(route('books.show', $book))
			->assertOk();

		$this->get(route('books.show', $translated_book))
			->assertOk();

		$this->get(route('books.show', $illustrated_book))
			->assertOk();

		$this->get(route('authors.show', $author))
			->assertOk();

		$author->refresh();

		$this->assertEquals(3, $author->view_day);
		$this->assertEquals(3, $author->view_week);
		$this->assertEquals(3, $author->view_month);
		$this->assertEquals(3, $author->view_year);
		$this->assertEquals(3, $author->view_all);
		$this->assertNotNull($author->view_updated_at);

		Carbon::setTestNow(now()->addDay());

		Artisan::call('clear:book_view_counts_period', ['period' => 'day']);
		Artisan::call('clear:book_view_ip');

		$this->get(route('books.show', $book))
			->assertOk();

		$this->get(route('books.show', $translated_book))
			->assertOk();

		$this->get(route('books.show', $illustrated_book))
			->assertOk();

		$this->get(route('authors.show', $author))
			->assertOk();

		$author->refresh();

		$this->assertEquals(3, $author->view_day);
		$this->assertEquals(6, $author->view_week);
		$this->assertEquals(6, $author->view_month);
		$this->assertEquals(6, $author->view_year);
		$this->assertEquals(6, $author->view_all);
		$this->assertNotNull($author->view_updated_at);
	}

	public function testShowPrivateHttp()
	{
		$author = Author::factory()->private()->create();

		$this->assertTrue($author->isPrivate());

		$this->get(route('authors.show', ['author' => $author]))
			->assertForbidden();
	}

	public function testDontSeeWrittenMinorBooks()
	{
		$author = Author::factory()->create();

		$mainBook = Book::factory()->with_minor_book()->create();

		$minorBook = $mainBook->groupedBooks()->first();

		$author->books()->sync([$mainBook->id, $minorBook->id]);

		$this->get(route('authors.show', $author))
			->assertOk()
			->assertSeeText($mainBook->title)
			->assertDontSeeText($minorBook->title)
			->assertViewHas('books_count', 1);
	}

	public function testSeeTranslatedMinorBooks()
	{
		$author = Author::factory()->create();

		$mainBook = Book::factory()->with_minor_book()->create();

		$minorBook = $mainBook->groupedBooks()->first();

		$author->translated_books()->sync([$mainBook->id, $minorBook->id]);

		$this->get(route('authors.show', $author))
			->assertOk()
			->assertSeeText($mainBook->title)
			->assertSeeText($minorBook->title)
			->assertViewHas('books_count', 2);
	}

	public function testSeeBookNotInGroup()
	{
		$author = Author::factory()->create();

		$mainBook = Book::factory()->create();

		$author->books()->sync([$mainBook->id]);

		$this->get(route('authors.show', $author))
			->assertOk()
			->assertSeeText($mainBook->title)
			->assertViewHas('books_count', 1);
	}

	public function testSeeOnReview()
	{
		$author = Author::factory()->sent_for_review()->create();

		$this->get(route('authors.show', $author))
			->assertOk()
			->assertSeeText(__('author.on_review_please_wait'));
	}
}
