<?php

namespace Tests\Feature\Book;

use App\Author;
use App\User;
use Tests\TestCase;

class BookBuyPolicyTest extends TestCase
{
	public function testCanBuyPolicy()
	{
		$author = factory(Author::class)
			->states('with_book_for_sale', 'with_author_manager_can_sell')
			->create();

		$buyer = factory(User::class)
			->create();

		$book = $author->books->first();

		$this->assertTrue($buyer->can('use_shop', User::class));
		$this->assertTrue($book->isForSale());
		$this->assertTrue($buyer->can('buy', $book));
	}

	public function testUserCantBuyABookIfAuthorSalesDisables()
	{
		$author = factory(Author::class)
			->states('with_book_for_sale', 'with_author_manager_can_sell')
			->create();

		$buyer = factory(User::class)
			->create();

		$book = $author->books->first();

		$this->assertTrue($buyer->can('use_shop', User::class));
		$this->assertTrue($book->isForSale());
		$this->assertTrue($buyer->can('buy', $book));

		$manager = $author->managers->first();

		$this->assertNotNull($manager);

		$manager->can_sale = false;
		$manager->save();

		$book->refresh();

		$this->assertFalse($buyer->can('buy', $book));
	}
}