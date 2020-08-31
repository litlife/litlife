<?php

namespace Tests\Feature\Book;

use App\Book;
use App\User;
use App\UserBook;
use Tests\TestCase;

class BookFavoritesTest extends TestCase
{
	public function testToggle()
	{
		$book = factory(Book::class)
			->create();

		$user = factory(User::class)
			->create();

		$this->actingAs($user)
			->get(route('books.favorites.toggle', ['book' => $book]))
			->assertOk()
			->assertJson([
				'result' => 'attached',
				'added_to_favorites_count' => 1
			]);

		$user->refresh();
		$book->refresh();

		$this->assertTrue($user->is($book->addedToFavoritesUsers()->first()));
		$this->assertEquals(1, $book->added_to_favorites_count);

		$this->actingAs($user)
			->get(route('books.favorites.toggle', ['book' => $book]))
			->assertOk()
			->assertJson([
				'result' => 'detached',
				'added_to_favorites_count' => 0
			]);

		$book->refresh();

		$this->assertEquals(0, $book->added_to_favorites_count);
	}

	public function testToggleIfAuthorDeleted()
	{
		$user_book = factory(UserBook::class)
			->create();

		$book = $user_book->book;
		$user = $user_book->user;

		$book->delete();

		$this->actingAs($user)
			->get(route('books.favorites.toggle', ['book' => $book]))
			->assertOk()
			->assertJson([
				'result' => 'detached',
				'added_to_favorites_count' => 0
			]);

		$book->refresh();

		$this->assertEquals(0, $book->added_to_favorites_count);
	}
}
