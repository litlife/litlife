<?php

namespace Tests\Feature\User;

use App\UserPurchase;
use Tests\TestCase;

class UserBooksPurchasedCountTest extends TestCase
{
	public function testPurchasedBooksCount()
	{
		$purchase = UserPurchase::factory()->book()->create();

		$purchase->buyer->purchasedBookCountRefresh();
		$purchase->refresh();

		$this->assertEquals(1, $purchase->buyer->data->books_purchased_count);
	}
}
