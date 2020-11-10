<?php

namespace Tests\Feature\User\Payments;

use App\UserPaymentTransaction;
use Illuminate\Support\Facades\Artisan;
use Litlife\Unitpay\Facades\UnitPay;
use Litlife\Unitpay\UnitPayApiResponse;
use Tests\TestCase;

class AutoUpdateIncomingWaitedPaymentsTest extends TestCase
{
	public function testUpdateProcessingPaymentTransaction()
	{
		$transaction = UserPaymentTransaction::factory()->incoming()->processing()->unitpay()->create(['sum' => '50']);

		$transaction->status_changed_at = now()->subHour();
		$transaction->push();

		$json = [
			'result' => [
				'status' => 'error',
				'projectId' => config('unitpay.project_id'),
				'paymentId' => $transaction->operable->payment_id,
				'account' => $transaction->id,
				'purse' => "481776xxxxxx1111",
				'profit' => $transaction->sum,
				'paymentType' => $transaction->operable->payment_type,
				'orderSum' => $transaction->sum,
				'orderCurrency' => "RUB",
				'date' => now()->toDateTimeString(),
				'payerSum' => "103.04",
				'payerCurrency' => "RUB",
				'availableForRefund' => '99.00',
				'isPreauth' => 0,
				'refunds' => [],
			]
		];

		UnitPay::shouldReceive('request')->once()->andReturn(new UnitPayApiResponse(json_encode($json)));
		UnitPay::makePartial();

		Artisan::call('payments:handle_waited_incoming', ['limit' => 100, 'latest_transaction_id' => $transaction->id]);

		$transaction->refresh();

		$this->assertTrue($transaction->isStatusError());
		$this->assertEquals($json, $transaction->operable->getParamsArray());
	}

	public function testTimeHasntPassedYet()
	{
		$transaction = UserPaymentTransaction::factory()->incoming()->processing()->unitpay()->create(['sum' => '50']);

		$transaction->status_changed_at = now();
		$transaction->push();

		Artisan::call('payments:handle_waited_incoming', ['limit' => 100, 'latest_transaction_id' => $transaction->id]);

		$transaction->refresh();

		$this->assertTrue($transaction->isStatusProcessing());
	}

	public function testPaymentIdIsNotSet()
	{
		$transaction = UserPaymentTransaction::factory()->incoming()->processing()->unitpay()->create(['sum' => '50']);

		$transaction->operable->payment_id = null;
		$transaction->push();

		Artisan::call('payments:handle_waited_incoming', ['limit' => 100, 'latest_transaction_id' => $transaction->id]);

		$transaction->refresh();

		$this->assertTrue($transaction->isStatusProcessing());
	}
}
