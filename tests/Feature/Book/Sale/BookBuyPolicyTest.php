<?php

namespace Tests\Feature\Book\Sale;

use App\Author;
use App\User;
use App\UserPurchase;
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

	public function testCantIfAuthorPolicy()
	{
		$author = factory(Author::class)
			->states('with_author_manager', 'with_book_for_sale')
			->create();

		$user = $author->managers->first()->user;
		$book = $author->books->first();

		$this->assertFalse($user->can('buy', $book));
	}

	public function testCantIfAlreadyBuyPolicy()
	{
		$purchase = factory(UserPurchase::class)
			->states('book')
			->create();

		$buyer = $purchase->buyer;
		$book = $purchase->purchasable;

		$this->assertFalse($buyer->can('buy', $book));
	}

	public function testCantSaleBookIfRemovedFromSale()
	{
		$author = factory(Author::class)
			->states('with_author_manager_can_sell', 'with_book')
			->create();

		$manager = $author->managers->first();
		$book = $author->books->first();
		$user = $manager->user;
		$book->statusReject();
		$book->price = null;
		$book->save();

		$this->assertFalse($user->can('sell', $book));
		$this->assertFalse($user->can('change_sell_settings', $book));

		$user = factory(User::class)->create();

		$this->assertFalse($user->can('buy_button', $book));
		$this->assertFalse($user->can('buy', $book));
	}
}