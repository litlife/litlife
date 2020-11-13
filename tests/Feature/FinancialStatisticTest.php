<?php

namespace Tests\Feature;

use App\User;
use App\UserPaymentTransaction;
use Litlife\Unitpay\Facades\UnitPay;
use Litlife\Unitpay\UnitPayApiResponse;
use Tests\TestCase;

class FinancialStatisticTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testUserPolicy()
    {
        $user = User::factory()->create();

        $this->assertFalse($user->can('view_financial_statistics', User::class));

        $user->group->view_financial_statistics = true;
        $user->push();
        $user->refresh();

        $this->assertTrue($user->can('view_financial_statistics', User::class));
    }

    public function testViewHttp()
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('financial_statistic.index'))
            ->assertForbidden();

        $user->group->view_financial_statistics = true;
        $user->push();
        $user->refresh();

        UnitPay::shouldReceive('request')
            ->once()
            ->andReturn(new UnitPayApiResponse(json_encode([
                'result' => [
                    'balance' => '14434.33',
                    'email' => 'partner@gmail.com',
                ]
            ])));

        UnitPay::makePartial();

        $this->actingAs($user)
            ->get(route('financial_statistic.index'))
            ->assertOk();
    }

    public function testViewAllTransactionsHttp()
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('financial_statistic.all_transactions'))
            ->assertForbidden();

        $user->group->view_financial_statistics = true;
        $user->push();
        $user->refresh();

        $this->actingAs($user)
            ->get(route('financial_statistic.all_transactions'))
            ->assertOk();
    }

    public function testViewAllTransactionsHttpSeeDepositPayment()
    {
        $user = User::factory()->create();
        $user->group->view_financial_statistics = true;
        $user->push();

        $transaction = UserPaymentTransaction::factory()
            ->incoming()
            ->wait()
            ->create([
                'sum' => 100,
                'user_id' => $user->id
            ]);

        $this->actingAs($user)
            ->get(route('financial_statistic.all_transactions'))
            ->assertOk()
            ->assertSeeText(__('user_payment_transaction.continue_pay'))
            ->assertSeeText(__('user_payment_transaction.cancel_incoming_payment'));
    }

    public function testViewAllTransactionsHttpSeeWithdrawalPayment()
    {
        $user = User::factory()->create();
        $user->group->view_financial_statistics = true;
        $user->push();

        $transaction = UserPaymentTransaction::factory()
            ->outgoing()->wait()
            ->create([
                'sum' => 100,
                'user_id' => $user->id
            ]);

        $this->actingAs($user)
            ->get(route('financial_statistic.all_transactions'))
            ->assertOk()
            ->assertSeeText(__('user_payment_transaction.cancel_outgoing_payment'));
    }

    public function testAllWaitedWithdrawalSum()
    {
        UserPaymentTransaction::withdrawal()
            ->wait()
            ->delete();

        $user = User::factory()->create();
        $user->group->view_financial_statistics = true;
        $user->push();

        $transaction = UserPaymentTransaction::factory()->outgoing()->wait()->create(['sum' => 100]);

        $transaction = UserPaymentTransaction::factory()->outgoing()->wait()->create(['sum' => 200]);

        $response = new UnitPayApiResponse(json_encode([
            'result' => [
                'balance' => '14434.33',
                'email' => 'partner@gmail.com',
            ]
        ]));

        UnitPay::shouldReceive('request')
            ->once()
            ->andReturn($response);

        UnitPay::makePartial();

        $response = $this->actingAs($user)
            ->get(route('financial_statistic.index'))
            ->assertOk()
            ->assertViewHas('all_waited_withdrawal_sum', 300)
            ->assertViewHas('request', $response)
            ->assertSeeText('14434.33');
    }

    public function testViewPurchases()
    {
        $user = User::factory()->create();
        $user->group->view_financial_statistics = true;
        $user->push();

        $this->actingAs($user)
            ->get(route('financial_statistic.purchases'))
            ->assertOk();
    }
}
