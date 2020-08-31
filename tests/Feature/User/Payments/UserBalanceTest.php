<?php

namespace Tests\Feature\User\Payments;

use App\User;
use App\UserPaymentTransaction;
use App\UserPurchase;
use Tests\TestCase;

class UserBalanceTest extends TestCase
{
	public function testIncomingSuccess()
	{
		$user = $this->getUserWithThousandMoneyOnBalance();

		$outgoing_payment = factory(UserPaymentTransaction::class)
			->states('incoming', 'success')
			->create(['user_id' => $user->id, 'sum' => 100]);

		$user->balance(true);
		$user->refresh();

		$this->assertEquals(1000, $user->fresh()->withdraw_balance());
		$this->assertEquals(0, $user->frozen_balance());
		$this->assertEquals(1100, $user->balance());
	}

	public function getUserWithThousandMoneyOnBalance()
	{
		return factory(User::class)
			->states('with_thousand_earned_money_on_balance')
			->create();
	}

	public function testIncomingWait()
	{
		$user = $this->getUserWithThousandMoneyOnBalance();

		$transaction = factory(UserPaymentTransaction::class)
			->states('incoming', 'wait', 'unitpay')
			->create(['user_id' => $user->id, 'sum' => 10]);

		$user->balance(true);
		$user->refresh();

		$this->assertEquals(1000, $user->fresh()->withdraw_balance());
		$this->assertEquals(0, $user->frozen_balance());
		$this->assertEquals(1000, $user->balance());
	}

	public function testIncomingProcessing()
	{
		$user = $this->getUserWithThousandMoneyOnBalance();

		$outgoing_payment = factory(UserPaymentTransaction::class)
			->states('incoming', 'processing')
			->create(['user_id' => $user->id, 'sum' => 100]);

		$user->balance(true);
		$user->refresh();

		$this->assertEquals(1000, $user->fresh()->withdraw_balance());
		$this->assertEquals(0, $user->frozen_balance());
		$this->assertEquals(1000, $user->balance());
	}

	public function testIncomingCanceled()
	{
		$user = $this->getUserWithThousandMoneyOnBalance();

		$outgoing_payment = factory(UserPaymentTransaction::class)
			->states('incoming', 'canceled')
			->create(['user_id' => $user->id, 'sum' => 100]);

		$user->balance(true);
		$user->refresh();

		$this->assertEquals(1000, $user->fresh()->withdraw_balance());
		$this->assertEquals(0, $user->frozen_balance());
		$this->assertEquals(1000, $user->balance());
	}

	public function testIncomingError()
	{
		$user = $this->getUserWithThousandMoneyOnBalance();

		$outgoing_payment = factory(UserPaymentTransaction::class)
			->states('incoming', 'error')
			->create(['user_id' => $user->id, 'sum' => 100]);

		$user->balance(true);
		$user->refresh();

		$this->assertEquals(1000, $user->fresh()->withdraw_balance());
		$this->assertEquals(0, $user->frozen_balance());
		$this->assertEquals(1000, $user->balance());
	}

	public function testOutgoingSuccess()
	{
		$user = $this->getUserWithThousandMoneyOnBalance();

		$transaction = factory(UserPaymentTransaction::class)
			->states('outgoing', 'success', 'unitpay')
			->create(['user_id' => $user->id, 'sum' => 100]);

		$user->balance(true);
		$user->refresh();

		$this->assertEquals(900, $user->fresh()->withdraw_balance());
		$this->assertEquals(0, $user->frozen_balance());
		$this->assertEquals(900, $user->balance());
	}

	public function testOutgoingWait()
	{
		$user = $this->getUserWithThousandMoneyOnBalance();

		$transaction = factory(UserPaymentTransaction::class)
			->states('outgoing', 'wait', 'unitpay')
			->create(['user_id' => $user->id, 'sum' => 100]);

		$user->balance(true);
		$user->refresh();

		$this->assertEquals(1000, $user->fresh()->withdraw_balance());
		$this->assertEquals(100, $user->frozen_balance());
		$this->assertEquals(900, $user->balance());
	}

	public function testOutgoingProcessing()
	{
		$user = $this->getUserWithThousandMoneyOnBalance();

		$transaction = factory(UserPaymentTransaction::class)
			->states('outgoing', 'processing', 'unitpay')
			->create(['user_id' => $user->id, 'sum' => 100]);

		$user->balance(true);
		$user->refresh();

		$this->assertEquals(900, $user->fresh()->withdraw_balance());
		$this->assertEquals(100, $user->frozen_balance());
		$this->assertEquals(900, $user->balance());
	}

	public function testOutgoingCanceled()
	{
		$user = $this->getUserWithThousandMoneyOnBalance();

		$transaction = factory(UserPaymentTransaction::class)
			->states('outgoing', 'canceled', 'unitpay')
			->create(['user_id' => $user->id, 'sum' => 100]);

		$user->balance(true);
		$user->refresh();

		$this->assertEquals(1000, $user->fresh()->withdraw_balance());
		$this->assertEquals(0, $user->frozen_balance());
		$this->assertEquals(1000, $user->balance());
	}

	public function testOutgoingError()
	{
		$user = $this->getUserWithThousandMoneyOnBalance();

		$transaction = factory(UserPaymentTransaction::class)
			->states('outgoing', 'error', 'unitpay')
			->create(['user_id' => $user->id, 'sum' => 100]);

		$user->balance(true);
		$user->refresh();

		$this->assertEquals(1000, $user->fresh()->withdraw_balance());
		$this->assertEquals(100, $user->frozen_balance());
		$this->assertEquals(900, $user->balance());
	}

	public function testTransferSuccess()
	{
		$user = $this->getUserWithThousandMoneyOnBalance();

		$transaction = factory(UserPaymentTransaction::class)
			->states('transfer', 'success')
			->create(['user_id' => $user->id, 'sum' => 100]);

		$user->balance(true);
		$user->refresh();

		$this->assertEquals(900, $user->withdraw_balance());
		$this->assertEquals(0, $user->frozen_balance());
		$this->assertEquals(900, $user->balance());
	}

	public function testTransferCancel()
	{
		$user = $this->getUserWithThousandMoneyOnBalance();

		$transaction = factory(UserPaymentTransaction::class)
			->states('transfer', 'canceled')
			->create(['user_id' => $user->id, 'sum' => 100]);

		$user->balance(true);
		$user->refresh();

		$this->assertEquals(1000, $user->withdraw_balance());
		$this->assertEquals(0, $user->frozen_balance());
		$this->assertEquals(1000, $user->balance());
	}

	public function testReceiptSuccess()
	{
		$user = $this->getUserWithThousandMoneyOnBalance();

		$transaction = factory(UserPaymentTransaction::class)
			->states('receipt', 'success', 'unitpay')
			->create(['user_id' => $user->id, 'sum' => 100]);

		$user->balance(true);
		$user->refresh();

		$this->assertEquals(1100, $user->withdraw_balance());
	}

	public function testBuyerReferComissionSuccess()
	{
		$purchase = factory(UserPurchase::class)
			->states('book', 'with_buyer_referer')
			->create();
		$purchase->referer_buyer_transaction->sum = 10;
		$purchase->push();

		$user = $purchase->buyer->referred_by_user->first();

		$user->balance(true);
		$user->refresh();

		$this->assertEquals(10, $user->fresh()->withdraw_balance());
	}

	public function testSellerReferComissionSuccess()
	{
		$purchase = factory(UserPurchase::class)
			->states('book', 'with_seller_referer')
			->create();
		$purchase->referer_seller_transaction->sum = 10;
		$purchase->push();

		$user = $purchase->seller->referred_by_user->first();

		$user->balance(true);
		$user->refresh();

		$this->assertEquals(10, $user->fresh()->withdraw_balance());
	}
}
