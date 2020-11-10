<?php

namespace Tests\Feature\User\Payments;

use App\UserPurchase;
use Tests\TestCase;

class UserPurchasedBooksTest extends TestCase
{
	/**
	 * A basic feature test example.
	 *
	 * @return void
	 */
	public function testSeeNotCanceledPurchase()
	{
		$purchase = UserPurchase::factory()->book()->create();

		$user = $purchase->buyer;
		$book = $purchase->purchasable;

		$this->actingAs($user)
			->get(route('users.books.purchased', ['user' => $user]))
			->assertOk()
			->assertSeeText($book->title);
	}

	/**
	 * A basic feature test example.
	 *
	 * @return void
	 */
	public function testDontSeeCanceledPurchase()
	{
		$purchase = UserPurchase::factory()->book()->canceled()->create();

		$user = $purchase->buyer;
		$book = $purchase->purchasable;

		$this->actingAs($user)
			->get(route('users.books.purchased', ['user' => $user]))
			->assertOk()
			->assertDontSeeText($book->title);
	}
}
