<?php

namespace Tests\Feature\Artisan;

use App\Author;
use App\Book;
use App\BookVote;
use App\Jobs\Book\BookGroupJob;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class BookGroupAllDuplicatesTest extends TestCase
{
	public function testIfDuplicatesExists()
	{
		$title = uniqid();

		$author = factory(Author::class)
			->create();

		$book = factory(Book::class)->create(['title' => $title]);
		$book2 = factory(Book::class)->create(['title' => $title]);
		$book3 = factory(Book::class)->create(['title' => $title]);
		$author->books()->sync([$book->id, $book2->id, $book3->id]);

		factory(BookVote::class)->create(['book_id' => $book2->id]);

		Artisan::call('book:group_all_duplicates', ['last_author_id' => $author->id]);

		$book->refresh();

		$this->assertEquals(2, $book->editions_count);

		$this->assertTrue($book->fresh()->isInGroup());
		$this->assertFalse($book->fresh()->isMainInGroup());

		$this->assertTrue($book2->fresh()->isInGroup());
		$this->assertTrue($book2->fresh()->isMainInGroup());

		$this->assertTrue($book3->fresh()->isInGroup());
		$this->assertFalse($book3->fresh()->isMainInGroup());
	}

	public function testIfDuplicatesNotExists()
	{
		$author = factory(Author::class)
			->create();

		$book = factory(Book::class)->create(['title' => uniqid()]);
		$book2 = factory(Book::class)->create(['title' => uniqid()]);
		$author->books()->sync([$book->id, $book2->id]);

		Artisan::call('book:group_all_duplicates', ['last_author_id' => $author->id]);

		$this->assertFalse($book->fresh()->isInGroup());
		$this->assertFalse($book2->fresh()->isInGroup());
	}

	public function testIfNoBooksExists()
	{
		$author = factory(Author::class)
			->create();

		Artisan::call('book:group_all_duplicates', ['last_author_id' => $author->id]);

		$this->assertTrue(true);
	}

	public function testBookAlreadyInGroup()
	{
		$title = uniqid();

		$author = factory(Author::class)->create();

		$book = factory(Book::class)->create(['title' => $title]);
		$book2 = factory(Book::class)->create(['title' => $title]);
		$author->books()->sync([$book->id, $book2->id]);

		BookGroupJob::dispatch($book, $book2);

		$this->assertTrue($book->fresh()->isInGroup());
		$this->assertTrue($book2->fresh()->isInGroup());

		Artisan::call('book:group_all_duplicates', ['last_author_id' => $author->id]);

		$this->assertEquals($book->id, $book2->fresh()->main_book_id);
	}

	public function testIfBookPrivate()
	{
		$title = uniqid();

		$author = factory(Author::class)
			->create();

		$book = factory(Book::class)->states('private')->create(['title' => $title]);
		$book2 = factory(Book::class)->create(['title' => $title]);
		$book3 = factory(Book::class)->states('private')->create(['title' => $title]);
		$author->books()->sync([$book->id, $book2->id, $book3->id]);

		Artisan::call('book:group_all_duplicates', ['last_author_id' => $author->id]);

		$book->refresh();

		$this->assertFalse($book->fresh()->isInGroup());
		$this->assertFalse($book2->fresh()->isInGroup());
		$this->assertFalse($book3->fresh()->isInGroup());
	}
}
