<?php

namespace Tests\Feature\Artisan;

use App\UserPaymentTransaction;
use Litlife\Unitpay\Facades\UnitPay;
use Litlife\Unitpay\UnitPayApiResponse;
use Tests\TestCase;

class IncomingPaymentUpdateTest extends TestCase
{
	public function testUpdate()
	{
		$transaction = factory(UserPaymentTransaction::class)
			->states('incoming', 'processing', 'unitpay')
			->create(['sum' => '50']);

		$buyer = $transaction->user;

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

		$this->artisan('payments:incoming_payment_update', ['transaction_id' => $transaction->id])
			->expectsOutput('Данные транзакции успешно обновлены')
			->assertExitCode(0);

		$transaction->refresh();

		$this->assertEquals($json, $transaction->operable->getParamsArray());

		$this->assertTrue($transaction->isStatusError());
	}

	public function testTransactionNotFound()
	{
		$transaction = factory(UserPaymentTransaction::class)
			->states('incoming', 'processing', 'unitpay')
			->create(['sum' => '50']);

		$id = $transaction->id;

		$transaction->forceDelete();

		$this->artisan('payments:incoming_payment_update', ['transaction_id' => $id])
			->expectsOutput('Транзакция с указанным ID: ' . $id . ' не найдена')
			->assertExitCode(0);
	}
}
