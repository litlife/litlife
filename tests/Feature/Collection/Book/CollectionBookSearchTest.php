<?php

namespace Tests\Feature\Collection\Book;

use App\Book;
use App\CollectedBook;
use App\Collection;
use App\User;
use Tests\TestCase;

class CollectionBookSearchTest extends TestCase
{
	public function testSearchById()
	{
		$collection = factory(Collection::class)
			->states('accepted')
			->create();

		$book = factory(Book::class)
			->create();

		$user = $collection->create_user;

		$this->actingAs($user)
			->ajax()
			->get(route('collections.books.list', ['collection' => $collection, 'query' => $book->id]))
			->assertOk()
			->assertViewIs('collection.book.list')
			->assertSeeText($book->title);
	}

	public function testSearchByIsbn()
	{
		$user = factory(User::class)->states('admin')->create();

		$title = uniqid();
		$isbn = rand(100, 999) . '-' . rand(1, 9) . '-' . rand(100, 999) . '-' . rand(10000, 99999) . '-' . rand(1, 9);

		$collected = factory(CollectedBook::class)
			->create();

		$book = $collected->book;
		$book->title = $title;
		$book->pi_isbn = $isbn;
		$book->save();

		$collection = factory(Collection::class)
			->create();

		$this->actingAs($user)
			->ajax()
			->get(route('collections.books.list', ['collection' => $collection, 'query' => $isbn]))
			->assertOk()
			->assertSeeText($book->title)
			->assertDontSeeText(__('In collection'));
	}

	public function testSearchByIsbnSeeBookInCollection()
	{
		$user = factory(User::class)->states('admin')->create();

		$title = uniqid();
		$isbn = rand(100, 999) . '-' . rand(1, 9) . '-' . rand(100, 999) . '-' . rand(10000, 99999) . '-' . rand(1, 9);

		$collected = factory(CollectedBook::class)
			->create();

		$collection = $collected->collection;
		$book = $collected->book;
		$book->title = $title;
		$book->pi_isbn = $isbn;
		$book->save();

		$this->actingAs($user)
			->ajax()
			->get(route('collections.books.list', ['collection' => $collection, 'query' => $isbn]))
			->assertOk()
			->assertSeeText($book->title)
			->assertSeeText(__('In collection'));
	}
}
