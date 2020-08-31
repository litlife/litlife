<?php

namespace Tests\Feature\User\Payments;

use App\Enums\PaymentStatusEnum;
use App\UserOutgoingPayment;
use Litlife\Unitpay\UnitPayFake;
use Tests\TestCase;

class UserOutgoingPaymentStatusTest extends TestCase
{
	public function testError()
	{
		$payment = factory(UserOutgoingPayment::class)
			->states('error')
			->create();

		$payment->params = ['error' => [
			'message' => __('user_outgoing_payment.errors.103'),
			'code' => '103',
		]];
		$payment->save();

		$this->assertEquals(3, PaymentStatusEnum::Error);
		$this->assertEquals('Error', $payment->transaction->status);
		$this->assertTrue($payment->transaction->isStatusError());
		$this->assertEquals('103', $payment->getErrorCode());
		$this->assertEquals(__('user_outgoing_payment.errors.103'), __('user_outgoing_payment.errors.' . $payment->getErrorCode()));
	}

	public function testSuccess()
	{
		$payment = factory(UserOutgoingPayment::class)
			->states('success')
			->create();

		$this->assertEquals(2, PaymentStatusEnum::Success);
		$this->assertEquals('Success', $payment->transaction->status);
		$this->assertTrue($payment->transaction->isStatusSuccess());
	}

	public function testWait()
	{
		$payment = factory(UserOutgoingPayment::class)
			->states('wait')
			->create();

		$this->assertEquals(0, PaymentStatusEnum::Wait);
		$this->assertEquals('Wait', $payment->transaction->status);
		$this->assertTrue($payment->transaction->isStatusWait());
	}

	public function testProcessing()
	{
		$payment = factory(UserOutgoingPayment::class)
			->states('processing')
			->create();

		$this->assertEquals(1, PaymentStatusEnum::Processing);
		$this->assertEquals('Processing', $payment->transaction->status);
		$this->assertTrue($payment->transaction->isStatusProcessing());
	}

	public function testCanceled()
	{
		$payment = factory(UserOutgoingPayment::class)
			->states('canceled')
			->create();

		$this->assertEquals(4, PaymentStatusEnum::Canceled);
		$this->assertEquals('Canceled', $payment->transaction->status);
		$this->assertTrue($payment->transaction->isStatusCanceled());
	}

	public function testChange()
	{
		$payment = factory(UserOutgoingPayment::class)
			->states('wait')
			->create();

		$this->assertTrue($payment->fresh()->transaction->isStatusWait());

		$payment->transaction->statusProcessing();
		$payment->push();

		$this->assertTrue($payment->fresh()->transaction->isStatusProcessing());

		$payment->transaction->statusError();
		$payment->push();

		$this->assertTrue($payment->fresh()->transaction->isStatusError());

		$payment->transaction->statusCanceled();
		$payment->push();

		$this->assertTrue($payment->fresh()->transaction->isStatusCanceled());

		$payment->transaction->statusSuccess();
		$payment->push();

		$this->assertTrue($payment->fresh()->transaction->isStatusSuccess());
	}
}
