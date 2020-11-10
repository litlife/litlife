<?php

namespace Tests\Feature\Author;

use App\Author;
use App\Book;
use App\User;
use Tests\TestCase;

class AuthorCloseAccessToBooksTest extends TestCase
{
	public function testCantCloseAccessIfNoPermission()
	{
		$admin = User::factory()->create();

		$author = Author::factory()->create();

		$admin->group->book_secret_hide_set = false;
		$admin->push();

		$this->assertFalse($admin->can('booksCloseAccess', $author));
	}

	public function testCanCloseAccessIfHasPermission()
	{
		$admin = User::factory()->create();

		$author = Author::factory()->create();

		$admin->group->book_secret_hide_set = true;
		$admin->push();

		$this->assertTrue($admin->can('booksCloseAccess', $author));
	}

	public function testBooksCloseAccess()
	{
		$admin = User::factory()->create();
		$admin->group->book_secret_hide_set = true;
		$admin->push();

		$author = Author::factory()->create();

		$book = Book::factory()->create();
		$translated_book = Book::factory()->create();
		$illustrated_book = Book::factory()->create();

		$author->books()->sync([$book->id]);
		$author->translated_books()->sync([$book->id]);
		$author->illustrated_books()->sync([$book->id]);
		$author->push();
		$author->save();

		$books = $author->any_books()->get();

		foreach ($books as $book) {
			$this->assertTrue($book->isReadAccess());
			$this->assertTrue($book->isDownloadAccess());
		}

		$this->actingAs($admin)
			->followingRedirects()
			->get(route('authors.books.close_access', $author))
			->assertOk()
			->assertSeeText(__('author.books_access_closed'));

		$books = $author->any_books()->get();

		foreach ($books as $book) {
			$this->assertFalse($book->isReadOrDownloadAccess());
		}
	}

}
