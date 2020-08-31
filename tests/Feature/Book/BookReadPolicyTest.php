<?php

namespace Tests\Feature\Book;

use App\Book;
use App\User;
use App\UserPurchase;
use Tests\TestCase;

class BookReadPolicyTest extends TestCase
{
	public function testCanReadIfBookPurchased()
	{
		$book = factory(Book::class)
			->states('with_section')
			->create(['price' => 100]);

		$user = factory(User::class)
			->create();

		$purchase = factory(UserPurchase::class)
			->create([
				'buyer_user_id' => $user->id,
				'purchasable_id' => $book->id,
				'purchasable_type' => 'book'
			]);

		$book->refresh();

		$this->assertEquals($purchase->purchasable->id, $book->id);

		$this->assertTrue($user->can('read', $book));
	}

	public function testCantReadBookIfPurchaseCanceled()
	{
		$book = factory(Book::class)
			->states('with_section')
			->create(['price' => 100]);

		$user = factory(User::class)
			->create();

		$purchase = factory(UserPurchase::class)
			->states('canceled')
			->create([
				'buyer_user_id' => $user->id,
				'purchasable_id' => $book->id,
				'purchasable_type' => 'book'
			]);

		$book->refresh();

		$this->assertEquals($purchase->purchasable->id, $book->id);

		$this->assertFalse($user->can('read', $book));
	}
}