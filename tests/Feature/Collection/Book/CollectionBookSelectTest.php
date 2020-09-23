<?php

namespace Tests\Feature\Collection\Book;

use App\Book;
use App\Collection;
use App\User;
use Tests\TestCase;

class CollectionBookSelectTest extends TestCase
{
	public function testBooksSelectHttp()
	{
		$user = factory(User::class)
			->states('admin')
			->create();

		$collection = factory(Collection::class)
			->states('accepted')
			->create(['who_can_add' => 'everyone']);

		$this->actingAs($user)
			->get(route('collections.books.select', $collection))
			->assertOk();
	}

	public function testIsOkIfOpenCollectionWithNoAccess()
	{
		$collection = factory(Collection::class)
			->states('private')
			->create();

		$this->get(route('collections.books.select', $collection))
			->assertStatus(401);
	}

	public function testSearchByIsbn()
	{
		$user = factory(User::class)->states('admin')->create();

		$title = uniqid();
		$isbn = rand(100, 999) . '-' . rand(1, 9) . '-' . rand(100, 999) . '-' . rand(10000, 99999) . '-' . rand(1, 9);

		$book = factory(Book::class)
			->create(['title' => $title, 'pi_isbn' => $isbn]);

		$this->actingAs($user)
			->get(route('collections.books.list', ['query' => $isbn]))
			->assertOk()
			->assertSeeText($book->title);
	}
}
