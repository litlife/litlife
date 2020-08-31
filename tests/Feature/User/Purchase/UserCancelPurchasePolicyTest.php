<?php

namespace Tests\Feature\User\Purchase;

use App\User;
use App\UserPurchase;
use Tests\TestCase;

class UserCancelPurchasePolicyTest extends TestCase
{
	/**
	 * A basic feature test example.
	 *
	 * @return void
	 */
	public function testCancelTrue()
	{
		$user = factory(User::class)->create();
		$user->group->view_financial_statistics = true;
		$user->push();

		$purchase = factory(UserPurchase::class)
			->states('book')
			->create();

		$this->assertTrue($user->can('cancel', $purchase));
	}

	/**
	 * A basic feature test example.
	 *
	 * @return void
	 */
	public function testCancelFalseIfPurchaseCanceled()
	{
		$user = factory(User::class)->create();
		$user->group->view_financial_statistics = true;
		$user->push();

		$purchase = factory(UserPurchase::class)
			->states('book', 'canceled')
			->create();

		$this->assertFalse($user->can('cancel', $purchase));
	}

	/**
	 * A basic feature test example.
	 *
	 * @return void
	 */
	public function testCancelFalseIfCantViewFinStatistics()
	{
		$user = factory(User::class)->create();
		$user->group->view_financial_statistics = false;
		$user->push();

		$purchase = factory(UserPurchase::class)
			->states('book')
			->create();

		$this->assertFalse($user->can('cancel', $purchase));
	}
}
