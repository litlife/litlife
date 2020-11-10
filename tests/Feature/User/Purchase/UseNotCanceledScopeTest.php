<?php

namespace Tests\Feature\User\Purchase;

use App\UserPurchase;
use Litlife\Unitpay\UnitPayFake;
use Tests\TestCase;

class UseNotCanceledScopeTest extends TestCase
{
	/**
	 * A basic feature test example.
	 *
	 * @return void
	 */
	public function testFoundNotCanceled()
	{
		$purchase = UserPurchase::factory()->book()->create();

		$this->assertEquals(1, UserPurchase::where('id', $purchase->id)
			->notCanceled()
			->count());
	}

	/**
	 * A basic feature test example.
	 *
	 * @return void
	 */
	public function testNotFoundCanceled()
	{
		$purchase = UserPurchase::factory()->book()->canceled()->create();

		$this->assertEquals(0, UserPurchase::where('id', $purchase->id)
			->notCanceled()
			->count());
	}
}
