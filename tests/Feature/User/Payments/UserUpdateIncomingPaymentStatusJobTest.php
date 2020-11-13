<?php

namespace Tests\Feature\User\Payments;

use App\Jobs\UpdateIncomingPaymentParamsJob;
use App\UserPaymentTransaction;
use Litlife\Unitpay\Facades\UnitPay;
use Litlife\Unitpay\UnitPayApiResponse;
use Tests\TestCase;

class UserUpdateIncomingPaymentStatusJobTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testIfErrorTransaction()
    {
        $transaction = UserPaymentTransaction::factory()->incoming()->processing()->unitpay()->create(['sum' => '50']);

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

        UpdateIncomingPaymentParamsJob::dispatch($transaction);

        $transaction->refresh();

        $this->assertEquals($json, $transaction->operable->getParamsArray());

        $this->assertTrue($transaction->isStatusError());
    }
}
