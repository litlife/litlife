<?php

namespace Tests\Feature\User\Payments;

use App\Author;
use App\Book;
use App\Notifications\InvoiceWasSuccessfullyPaidNotification;
use App\User;
use App\UserIncomingPayment;
use App\UserOutgoingPayment;
use App\UserPaymentTransaction;
use App\UserPurchase;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Litlife\Unitpay\Facades\UnitPay;
use Litlife\Unitpay\UnitPayApiResponse;
use Litlife\Unitpay\UnitPayFake;
use Tests\TestCase;
use TiMacDonald\Log\LogFake;

class UserPaymentsTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testSetBalanceAttribute()
    {
        $user = User::factory()->create();

        $user->balance = 1.123456;
        $user->save();

        $this->assertEquals('1.12', $user->fresh()->balance);

        $user->balance = 12345678.1234;
        $user->save();

        $this->assertEquals('12345678.12', $user->fresh()->balance);
    }

    public function testRefreshBalance()
    {
        $user = User::factory()->withMoneyOnBalance()->create();

        $this->assertEquals(1000, $user->balance);

        $outgoing_payment = UserPaymentTransaction::factory()->outgoing()->create(['user_id' => $user->id, 'sum' => 100]);

        $user->refresh();

        $this->assertEquals(900, $user->balance);
    }

    public function testFrozenBalance()
    {
        $user = User::factory()->withMoneyOnBalance()->create();

        $this->assertEquals(1000, $user->balance());
        $this->assertEquals(0, $user->frozen_balance());

        $outgoing_transaction = UserPaymentTransaction::factory()
            ->outgoing()
            ->wait()
            ->create(['user_id' => $user->id, 'sum' => 100]);

        $this->assertTrue($outgoing_transaction->isStatusWait());
        $this->assertTrue($outgoing_transaction->isWithdrawal());

        $user->refresh();

        $this->assertEquals(100, $user->frozen_balance());
        $this->assertEquals(900, $user->balance());
    }

    public function testBalanceForWaitedDeposit()
    {
        $user = User::factory()->create()
            ->fresh();

        $this->assertEquals(0, $user->balance());
        $this->assertEquals(0, $user->frozen_balance());

        $payment = UserIncomingPayment::factory()->wait()->create();

        $user->balance(true);

        $this->assertEquals(0, $user->frozen_balance());
        $this->assertEquals(0, $user->balance());
    }

    public function testBalanceForCancelIncomingPayment()
    {
        $user = User::factory()->create()
            ->fresh();

        $this->assertEquals(0, $user->balance());
        $this->assertEquals(0, $user->frozen_balance());

        $transaction = UserPaymentTransaction::factory()
            ->incoming()
            ->canceled()
            ->create(['user_id' => $user->id, 'sum' => 100]);

        $user->balance(true);

        $this->assertEquals(0, $user->frozen_balance());
        $this->assertEquals(0, $user->balance());
    }

    public function testMorphRelation()
    {
        $incoming_payment = UserIncomingPayment::factory()->unitpay_success()->create();

        $this->assertEquals($incoming_payment->transaction->fresh(), $incoming_payment->transaction->operable->transaction);

        $outgoing_payment = UserOutgoingPayment::factory()->success()->create();

        $this->assertEquals($outgoing_payment->transaction->fresh(), $outgoing_payment->transaction->operable->transaction);

        $payment = UserIncomingPayment::factory()->wait()->create();

        $transaction = new UserPaymentTransaction;
        $transaction->user_id = 50000;
        $transaction->sum = 100;
        $transaction->type = 0;
        $transaction->statusSuccess();

        $payment->transaction()->save($transaction);
    }

    public function testPaymentHandler()
    {
        $sum = '50';

        $buyer = User::factory()->create();

        $transaction = UserPaymentTransaction::factory()
            ->incoming()
            ->wait()
            ->create([
                'sum' => $sum,
                'user_id' => $buyer->id
            ]);

        $this->assertTrue($transaction->isStatusWait());
        $this->assertEquals(0, $buyer->frozen_balance());
        $this->assertNull($transaction->operable->payment_id);

        $params = [
            'unitpayId' => rand(10000, 1000000),
            'projectId' => config('unitpay.project_id'),
            'account' => $transaction->id,
            'payerSum' => $transaction->sum + 10,
            'payerCurrency' => 'RUB',
            'profit' => $transaction->sum,
            'paymentType' => $transaction->operable->payment_type,
            'orderSum' => $transaction->sum,
            'orderCurrency' => 'RUB',
            'date' => now()->toDateTimeString(),
            'test' => '0'
        ];

        $this->json('get', route('unitpay.handler'),
            [
                'method' => 'check',
                'params' => array_merge($params, ['signature' => UnitPay::getSignature('check', $params)])
            ])
            ->assertJson(['result' => ['message' => 'Запрос успешно обработан']]);

        $transaction->refresh();

        $this->assertTrue($transaction->isStatusProcessing());

        UnitPay::shouldReceive('request')
            ->once()
            ->andReturn(new UnitPayApiResponse(json_encode([
                'result' => [
                    'paymentId' => config('unitpay.project_id'),
                    'status' => 'success',
                    'paymentType' => 'webmoney',
                    'date' => now()->toDateTimeString(),
                    'purse' => "R123456789123",
                    'account' => $transaction->id,
                    'profit' => "4.93",
                    'projectId' => config('unitpay.project_id'),
                    'orderSum' => "5.00",
                    'orderCurrency' => "RUB",
                    'payerSum' => "5.00",
                    'payerCurrency' => "RUB",
                    'availableForRefund' => '',
                    'refunds' => []
                ]
            ])));
        UnitPay::makePartial();

        $this->json('get', route('unitpay.handler'),
            [
                'method' => 'pay',
                'params' => array_merge($params, ['signature' => UnitPay::getSignature('pay', $params)])
            ])
            ->assertJson(['result' => ['message' => 'Запрос успешно обработан']]);

        $transaction->refresh();

        $this->assertTrue($transaction->isStatusSuccess());

        $buyer->refresh();

        $this->assertEquals($sum, $buyer->balance());
        $this->assertEquals(0, $buyer->frozen_balance());
    }

    public function testHandlerSendPayStatusSecondTime()
    {
        $sum = '50';

        $buyer = User::factory()->create();

        $transaction = UserPaymentTransaction::factory()
            ->incoming()
            ->success()
            ->unitpay()
            ->create([
                'sum' => $sum,
                'user_id' => $buyer->id
            ]);

        $buyer->refresh();

        $this->assertEquals($sum, $buyer->balance);

        $params = [
            'unitpayId' => $transaction->operable->payment_id,
            'projectId' => config('unitpay.project_id'),
            'account' => $transaction->id,
            'payerSum' => $transaction->sum + 10,
            'payerCurrency' => 'RUB',
            'profit' => $transaction->sum,
            'paymentType' => $transaction->operable->payment_type,
            'orderSum' => $transaction->sum,
            'orderCurrency' => 'RUB',
            'date' => now()->toDateTimeString(),
            'test' => '0'
        ];

        $this->json('get', route('unitpay.handler'),
            [
                'method' => 'pay',
                'params' => array_merge($params, ['signature' => UnitPay::getSignature('pay', $params)])
            ])
            ->assertJson(['result' => ['message' => 'Запрос успешно обработан']]);

        $transaction->refresh();

        $this->assertTrue($transaction->isStatusSuccess());

        $buyer->refresh();

        $this->assertEquals($sum, $buyer->balance);
    }

    public function testHandlerWrongIp()
    {
        $sum = '50';

        $buyer = User::factory()->create();

        $transaction = UserPaymentTransaction::factory()
            ->incoming()
            ->success()
            ->unitpay()
            ->create([
                'sum' => $sum,
                'user_id' => $buyer->id
            ]);

        $buyer->refresh();

        $this->assertEquals($sum, $buyer->balance);

        $params = [
            'unitpayId' => $transaction->operable->payment_id,
            'projectId' => config('unitpay.project_id'),
            'account' => $buyer->id,
            'payerSum' => $transaction->sum + 10,
            'payerCurrency' => 'RUB',
            'profit' => $transaction->sum,
            'paymentType' => $transaction->operable->payment_type,
            'orderSum' => $transaction->sum,
            'orderCurrency' => 'RUB',
            'date' => now()->toDateTimeString(),
            'test' => '0'
        ];

        $this->json('get', route('unitpay.handler'),
            [
                'method' => 'pay',
                'params' => array_merge($params, ['signature' => UnitPay::getSignature('pay', $params)])
            ], ['REMOTE_ADDR' => '1.2.3.4'])
            ->assertJson([
                'error' =>
                    ['message' => 'Ip is wrong']
            ]);
    }

    public function testHandlerWrongSignature()
    {
        $sum = '50';

        $buyer = User::factory()->create();

        $transaction = UserPaymentTransaction::factory()
            ->incoming()
            ->success()
            ->unitpay()
            ->create([
                'sum' => $sum,
                'user_id' => $buyer->id
            ]);

        $buyer->refresh();

        $this->assertEquals($sum, $buyer->balance);

        $params = [
            'unitpayId' => $transaction->operable->payment_id,
            'projectId' => config('unitpay.project_id'),
            'account' => $transaction->id,
            'payerSum' => $transaction->sum + 10,
            'payerCurrency' => 'RUB',
            'profit' => $transaction->sum,
            'paymentType' => $transaction->operable->payment_type,
            'orderSum' => $transaction->sum,
            'orderCurrency' => 'RUB',
            'date' => now()->toDateTimeString(),
            'test' => '0'
        ];

        $this->json('get', route('unitpay.handler'),
            [
                'method' => 'pay',
                'params' => array_merge($params, ['signature' => 'symbol'.UnitPay::getSignature('pay', $params)])
            ])
            ->assertJson([
                'error' =>
                    ['message' => 'Signature is wrong']
            ]);
    }

    public function testErrorCheck()
    {
        $transaction = UserPaymentTransaction::factory()->incoming()->processing()->unitpay()->create(['sum' => '50']);

        $buyer = $transaction->user;

        $json = [
            'result' => [
                'projectId' => config('unitpay.project_id'),
                'paymentId' => $transaction->operable->payment_id,
                'account' => $transaction->id,
                'purse' => "526280xxxxx3104",
                'profit' => $transaction->sum,
                'paymentType' => $transaction->operable->payment_type,
                'orderSum' => $transaction->sum,
                'orderCurrency' => "RUB",
                'date' => now()->toDateTimeString(),
                'payerSum' => "10.00",
                'payerCurrency' => "RUB",
                'errorMessage' => "Пользователь отказался от оплаты"
            ]
        ];

        UnitPay::shouldReceive('request')->once()->andReturn(new UnitPayApiResponse(json_encode($json)));
        UnitPay::makePartial();

        $params = [
            'unitpayId' => $transaction->operable->payment_id,
            'account' => $transaction->id,
            'payerSum' => $transaction->sum + 10,
            'payerCurrency' => 'RUB',
            'profit' => $transaction->sum,
            'paymentType' => $transaction->operable->payment_type,
            'orderSum' => $transaction->sum,
            'orderCurrency' => 'RUB',
            'date' => now()->toDateTimeString(),
            'test' => '0'
        ];

        $this->json('get', route('unitpay.handler'),
            [
                'method' => 'error',
                'params' => array_merge($params, ['signature' => UnitPay::getSignature('error', $params)])
            ])
            ->assertJson(['result' => ['message' => 'Запрос успешно обработан']]);

        $transaction->refresh();

        $this->assertEquals($json, $transaction->operable->getParamsArray());
        $this->assertEquals($json['result']['errorMessage'], $transaction->operable->getPaymentError());
    }

    public function testSeller()
    {
        $author = Author::factory()->with_book_for_sale()->with_author_manager_can_sell()->create();

        $book = $author->books->first();

        $this->assertEquals($author->seller()->id, $book->seller()->id);
    }

    public function testTryBuyWithNotEnoughMoney()
    {
        $author = Author::factory()->with_book_for_sale()->with_author_manager_can_sell()->create();

        $book = $author->books->first();
        $book->price = 100;
        $book->save();

        $seller = $author->seller();

        $buyer = User::factory()->create();
        $buyer->balance = 99;
        $buyer->save();

        $this->actingAs($buyer)
            ->get(route('books.buy', $book))
            ->assertRedirect(route('books.show', $book));
    }

    public function testLog()
    {
        config(['unitpay.log_enable' => true]);
        config(['unitpay.log_chanel' => 'daily']);

        Log::swap(new LogFake);

        $query = [
            'method' => 'test',
        ];

        $contents = 'test';

        UnitPay::log($query, $contents);

        $str = 'Request: '.json_encode($query)."\n".'Response: '.$contents;

        Log::assertLogged('info', function ($message, $context) use ($str) {
            return Str::contains($message, '"method":"test"');
        });
    }

    public function testSeeDescriptionDeposit()
    {
        $transaction = UserPaymentTransaction::factory()->incoming()->success()->unitpay()->create();

        $this->actingAs($transaction->user)
            ->get(route('users.wallet', ['user' => $transaction->user]))
            ->assertOk()
            ->assertDontSeeText(__('user_payment_transaction.nothing_found'))
            ->assertSeeText(__('user_payment_transaction.deposit.Success', [
                'payment_type' => __('user_payment_transaction.payment_types_for_array.'.$transaction->operable->params->result->paymentType),
                'purse' => $transaction->operable->params->result->purse,
                'sum' => $transaction->sum
            ]));
    }

    public function testSeeDescriptionWithdrawal()
    {
        $transaction = UserPaymentTransaction::factory()->outgoing()->success()->unitpay()->create();

        $this->actingAs($transaction->user)
            ->get(route('users.wallet', ['user' => $transaction->user]))
            ->assertOk()
            ->assertDontSeeText(__('user_payment_transaction.nothing_found'))
            ->assertSeeText(__('user_payment_transaction.withdrawal.Success', [
                'payment_type' => __('user_payment_transaction.payment_types_for_array.'.$transaction->operable->payment_type),
                'purse' => $transaction->operable->purse,
                'sum' => abs($transaction->sum) - $transaction->operable->getPayoutComission(),
                'comission' => $transaction->operable->getPayoutComission()
            ]));
    }

    public function testSeeDescriptionWithdrawalProcessing()
    {
        $transaction = UserPaymentTransaction::factory()->outgoing()->processing()->unitpay()->create();

        $this->assertEquals('0.45', $transaction->operable->getPayoutComission());

        $this->actingAs($transaction->user)
            ->get(route('users.wallet', ['user' => $transaction->user]))
            ->assertOk()
            ->assertDontSeeText(__('user_payment_transaction.nothing_found'))
            ->assertSeeText(__('user_payment_transaction.withdrawal.Processing', [
                'payment_type' => __('user_payment_transaction.payment_types_for_array.'.$transaction->operable->payment_type),
                'purse' => $transaction->operable->purse,
                'sum' => abs($transaction->sum) - $transaction->operable->getPayoutComission(),
                'comission' => $transaction->operable->getPayoutComission()
            ]));
    }

    public function testSeeDescriptionSellBuy()
    {
        $purchase = UserPurchase::factory()->book()->create();

        $this->actingAs($purchase->seller)
            ->get(route('users.wallet', ['user' => $purchase->seller]))
            ->assertOk()
            ->assertDontSeeText(__('user_payment_transaction.nothing_found'))
            ->assertSeeText(__('user_payment_transaction.sell', [
                'title' => $purchase->purchasable->title,
                'sum' => $purchase->seller_transaction->sum
            ]));

        $this->actingAs($purchase->buyer)
            ->get(route('users.wallet', ['user' => $purchase->buyer]))
            ->assertOk()
            ->assertDontSeeText(__('user_payment_transaction.nothing_found'))
            ->assertSeeText(__('user_payment_transaction.buy', [
                'title' => $purchase->purchasable->title,
                'sum' => abs($purchase->buyer_transaction->sum)
            ]));
    }

    public function testSeeDescriptionTransactionErrorButWithWaitStatusReceived()
    {
        $transaction = UserPaymentTransaction::factory()->incoming()->error()->unitpay()->create();

        $json = [
            'result' => [
                'status' => 'wait',
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

        $transaction->operable->params = $json;
        $transaction->push();

        $this->actingAs($transaction->user)
            ->get(route('users.wallet', ['user' => $transaction->user]))
            ->assertOk()
            ->assertDontSeeText(__('user_payment_transaction.nothing_found'))
            ->assertSeeText(__('user_payment_transaction.deposit.Error', [
                'payment_type' => __('user_payment_transaction.payment_types_for_array.'.$transaction->operable->payment_type),
                'purse' => $transaction->operable->getPurse(),
                'sum' => abs($transaction->sum)
            ]));
    }

    public function testSeeDescriptionSellBuyWithBuyerReferAndSellerRefer()
    {
        $purchase = UserPurchase::factory()->book()->with_seller_referer()->with_buyer_referer()->create();

        $seller_referer = $purchase->seller->referred_by_user->first();
        $buyer_referer = $purchase->buyer->referred_by_user->first();

        $this->assertNotNull($seller_referer);
        $this->assertNotNull($buyer_referer);
        $this->assertNotNull($purchase->referer_seller_transaction);
        $this->assertNotNull($purchase->referer_buyer_transaction);
        $this->assertEquals($seller_referer->id, $purchase->referer_seller_transaction->user->id);
        $this->assertEquals($buyer_referer->id, $purchase->referer_buyer_transaction->user->id);
        $this->assertTrue($purchase->referer_seller_transaction->isComissionRefererSeller());
        $this->assertTrue($purchase->referer_buyer_transaction->isComissionRefererBuyer());

        $this->actingAs($seller_referer)
            ->get(route('users.wallet', ['user' => $seller_referer]))
            ->assertOk()
            ->assertDontSeeText(__('user_payment_transaction.nothing_found'))
            ->assertSeeText(__('user_payment_transaction.comission_for_refer_seller', [
                'user_name' => $purchase->seller->userName,
                'sum' => $purchase->referer_seller_transaction->sum
            ]));

        $this->actingAs($buyer_referer)
            ->get(route('users.wallet', ['user' => $buyer_referer]))
            ->assertOk()
            ->assertDontSeeText(__('user_payment_transaction.nothing_found'))
            ->assertSeeText(__('user_payment_transaction.comission_for_refer_buyer', [
                'user_name' => $purchase->buyer->userName,
                'sum' => abs($purchase->referer_buyer_transaction->sum)
            ]));
    }

    public function testDepositFormHttp()
    {
        $user = User::factory()->create()
            ->fresh();

        $this->actingAs($user)
            ->get(route('users.wallet.deposit', ['user' => $user]))
            ->assertOk();
    }

    public function testDepositPayHttp()
    {
        $user = User::factory()->create()
            ->fresh();

        $payments_type = config('unitpay.allowed_payment_types');

        $sum = $this->faker->numberBetween(30, 200);
        $type = $payments_type[array_rand($payments_type)];

        $response = $this->actingAs($user)
            ->post(route('users.wallet.deposit.pay', ['user' => $user]),
                [
                    'sum' => $sum,
                    'payment_type' => $type,
                ])
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        $payment = $user->incoming_payment()->get()->first();

        $params['sum'] = $sum;
        $params['account'] = $payment->transaction->id;
        $params['desc'] = __('user_incoming_payment.desc', ['user_id' => $user->name, 'sum' => $params['sum']]);
        $params['currency'] = 'RUB';
        $params['backUrl'] = route('users.wallet', ['user' => $user]);

        $url = UnitPay::getFormUrl($type, $params);

        $response->assertRedirect($url);
    }

    public function testDepositForBuyABook()
    {
        $user = User::factory()->create()
            ->fresh();

        $author = Author::factory()->with_book_for_sale()->with_author_manager_can_sell()->create();

        $book = $author->books->first();
        $book->price = 10;
        $book->save();

        $this->assertEquals(10, $book->price);

        $payments_type = config('unitpay.allowed_payment_types');

        $type = $payments_type[array_rand($payments_type)];

        $response = $this->actingAs($user)
            ->post(route('books.buy.deposit', ['book' => $book]),
                ['payment_type' => $type])
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        $payment = $user->incoming_payment->first();

        $params['sum'] = $book->price;
        $params['account'] = $payment->transaction->id;
        $params['desc'] = __('user_incoming_payment.desc_buy_book', ['title' => $book->getSellTitle(), 'sum' => $params['sum']]);
        $params['currency'] = 'RUB';
        $params['backUrl'] = route('books.show', ['book' => $book]);

        $url = UnitPay::getFormUrl($type, $params);

        $response->assertRedirect($url);

        $this->assertEquals($type, $payment->payment_type);
        $this->assertEquals($user->id, $payment->user_id);
        $this->assertEquals($book->price, $payment->transaction->sum);
        $this->assertEquals('RUB', $payment->currency);
        $this->assertEquals('unitpay', $payment->payment_aggregator);

        $this->assertEquals($user->id, $payment->transaction->user_id);
        $this->assertEquals($book->price, $payment->transaction->sum);
        $this->assertTrue($payment->transaction->isStatusWait());
        $this->assertTrue($payment->transaction->isDeposit());
        $this->assertEquals($payment->transaction->params, (object) ['buy_book' => $book->id]);
    }

    public function testDepositSuccessUrlRedirectBuyABook()
    {
        $transaction = UserPaymentTransaction::factory()->incoming()->success()->unitpay()->create();

        $book = Book::factory()->create(['price' => $transaction->sum]);

        $transaction->params = ['buy_book' => $book->id];
        $transaction->statusProcessing();
        $transaction->push();

        $this->assertNotNull($transaction->operable->payment_id);

        $json = [
            'result' => [
                'paymentId' => config('unitpay.project_id'),
                'status' => 'success',
                'paymentType' => 'webmoney',
                'date' => now()->toDateTimeString(),
                'purse' => "R123456789123",
                'account' => $transaction->id,
                'profit' => "4.93",
                'projectId' => config('unitpay.project_id'),
                'orderSum' => "5.00",
                'orderCurrency' => "RUB",
                'payerSum' => "5.00",
                'payerCurrency' => "RUB",
                'availableForRefund' => '',
                'refunds' => []
            ]
        ];

        UnitPay::shouldReceive('request')->once()->andReturn(new UnitPayApiResponse(json_encode($json)));
        UnitPay::makePartial();

        $this->assertTrue($transaction->isStatusProcessing());

        $this->actingAs($transaction->user)
            ->get(route('unitpay.payment.success', ['account' => $transaction->id, 'paymentId' => $transaction->operable->payment_id]))
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('books.buy', ['book' => $book]));

        $transaction->refresh();

        $this->assertTrue($transaction->isStatusSuccess());

        $this->assertEquals($book->price, $transaction->user->balance);
    }

    public function testDepositSuccessUrl()
    {
        Notification::fake();

        $transaction = UserPaymentTransaction::factory()->incoming()->success()->unitpay()->create();

        $book = Book::factory()->create();
        $transaction->statusProcessing();
        $transaction->push();

        $user = $transaction->user;

        $json = [
            'result' => [
                'paymentId' => config('unitpay.project_id'),
                'status' => 'success',
                'paymentType' => 'webmoney',
                'date' => now()->toDateTimeString(),
                'purse' => "R123456789123",
                'account' => $transaction->id,
                'profit' => "4.93",
                'projectId' => config('unitpay.project_id'),
                'orderSum' => "5.00",
                'orderCurrency' => "RUB",
                'payerSum' => "5.00",
                'payerCurrency' => "RUB",
                'availableForRefund' => '',
                'refunds' => []
            ]
        ];

        UnitPay::shouldReceive('request')->once()->andReturn(new UnitPayApiResponse(json_encode($json)));
        UnitPay::makePartial();

        $this->assertTrue($transaction->isStatusProcessing());

        $this->actingAs($transaction->user)
            ->get(route('unitpay.payment.success', ['account' => $transaction->id, 'paymentId' => $transaction->operable->payment_id]))
            ->assertRedirect(route('users.wallet', ['user' => $transaction->user]));

        $transaction->refresh();

        $this->assertTrue($transaction->isStatusSuccess());

        Notification::assertSentTo(
            $user,
            InvoiceWasSuccessfullyPaidNotification::class,
            function ($notification, $channels) use ($transaction, $user) {

                $this->assertContains('mail', $channels);

                $mail = $notification->toMail($user);

                $this->assertEquals(__('notification.invoice_was_successfully_paid.subject'), $mail->subject);
                $this->assertEquals(__('notification.invoice_was_successfully_paid.line',
                    ['sum' => $transaction->sum]), $mail->introLines[0]);
                $this->assertEquals(route('users.wallet', ['user' => $user]), $mail->actionUrl);
                $this->assertEquals(__('notification.invoice_was_successfully_paid.action'), $mail->actionText);

                return $notification->transaction->id == $transaction->id;
            }
        );
    }

    public function testDepositErrorUrl()
    {
        $transaction = UserPaymentTransaction::factory()->incoming()->error()->unitpay()->create();

        $json = [
            'result' => [
                'paymentId' => config('unitpay.project_id'),
                'status' => 'error',
                'paymentType' => 'webmoney',
                'date' => now()->toDateTimeString(),
                'purse' => "R123456789123",
                'account' => $transaction->id,
                'profit' => "4.93",
                'projectId' => config('unitpay.project_id'),
                'orderSum' => "5.00",
                'orderCurrency' => "RUB",
                'payerSum' => "5.00",
                'payerCurrency' => "RUB",
                'availableForRefund' => '',
                'refunds' => []
            ]
        ];

        UnitPay::shouldReceive('request')->once()->andReturn(new UnitPayApiResponse(json_encode($json)));
        UnitPay::makePartial();

        $this->assertTrue($transaction->isStatusError());

        $response = $this->actingAs($transaction->user)
            ->get(route('unitpay.payment.error', ['account' => $transaction->id, 'paymentId' => $transaction->operable->payment_id]))
            ->assertSessionHasErrors(['errors' => __('user_incoming_payment.error')])
            ->assertRedirect(route('users.wallet', ['user' => $transaction->user]));
    }

    public function testTransactionPayPolicy()
    {
        $transaction = UserPaymentTransaction::factory()->incoming()->processing()->unitpay()->create();

        $this->assertTrue($transaction->user->can('pay', $transaction));

        $transaction = UserPaymentTransaction::factory()->incoming()->wait()->unitpay()->create();

        $user = User::factory()->create();

        $this->assertFalse($user->can('pay', $transaction));


        $transaction = UserPaymentTransaction::factory()->incoming()->wait()->unitpay()->create();

        $this->assertTrue($transaction->user->can('pay', $transaction));

        $transaction = UserPaymentTransaction::factory()->outgoing()->wait()->unitpay()->create();

        $this->assertFalse($transaction->user->can('pay', $transaction));
    }

    public function testTransactionCancelPolicy()
    {
        $transaction = UserPaymentTransaction::factory()->incoming()->processing()->unitpay()->create();

        $this->assertFalse($transaction->user->can('cancel', $transaction));


        $transaction = UserPaymentTransaction::factory()->incoming()->wait()->unitpay()->create();

        $user = User::factory()->create();

        $this->assertFalse($user->can('cancel', $transaction));


        $transaction = UserPaymentTransaction::factory()->incoming()->wait()->unitpay()->create();

        $this->assertTrue($transaction->user->can('cancel', $transaction));
    }

    public function testTransactionCancel()
    {
        $transaction = UserPaymentTransaction::factory()->incoming()->wait()->unitpay()->create();

        $user = $transaction->user;

        $this->assertTrue($transaction->isStatusWait());
        $this->assertEquals(0, $user->balance(true));

        $this->actingAs($user)
            ->get(route('users.transaction.cancel', ['user' => $user, 'transaction' => $transaction]))
            ->assertRedirect(route('users.wallet', $user));

        $transaction->refresh();

        $this->assertTrue($transaction->isStatusCanceled());
        $this->assertEquals(0, $user->balance(true));
    }

    public function testTransactionPayWaited()
    {
        $transaction = UserPaymentTransaction::factory()->incoming()->wait()->unitpay()->create();

        $user = $transaction->user;

        $this->assertTrue($transaction->isStatusWait());
        $this->assertEquals(0, $user->balance(true));

        $response = $this->actingAs($user)
            ->get(route('users.transaction.pay', ['user' => $user, 'transaction' => $transaction]))
            ->assertRedirect();

        $params['sum'] = $transaction->sum;
        $params['type'] = $transaction->operable->payment_type;
        $params['account'] = $transaction->id;
        $params['desc'] = __('user_incoming_payment.desc', ['user_id' => $user->name, 'sum' => $params['sum']]);
        $params['currency'] = 'RUB';
        $params['backUrl'] = route('users.wallet', ['user' => $user]);

        $url = UnitPay::getFormUrl($params['type'], $params);

        $response->assertRedirect($url);
    }

    public function testPaymentHandlerChangePaymentMethodAfterOrderCreated()
    {
        $sum = '50';

        $buyer = User::factory()->create();

        $transaction = UserPaymentTransaction::factory()
            ->incoming()
            ->wait()
            ->unitpay()
            ->create([
                'sum' => $sum,
                'user_id' => $buyer->id
            ]);

        $transaction->operable->payment_type = 'card';
        $transaction->push();

        $this->assertTrue($transaction->isStatusWait());
        $this->assertEquals(0, $buyer->frozen_balance());
        $this->assertEquals('card', $transaction->operable->payment_type);

        $params = [
            'unitpayId' => rand(1000, 100000),
            'projectId' => config('unitpay.project_id'),
            'account' => $transaction->id,
            'payerSum' => $transaction->sum + 10,
            'payerCurrency' => 'RUB',
            'profit' => $transaction->sum,
            'paymentType' => 'webmoney',
            'orderSum' => $transaction->sum,
            'orderCurrency' => 'RUB',
            'date' => now()->toDateTimeString(),
            'test' => '0'
        ];

        $this->json('get', route('unitpay.handler'),
            [
                'method' => 'check',
                'params' => array_merge($params, ['signature' => UnitPay::getSignature('check', $params)])
            ])
            ->assertJson(['result' => ['message' => 'Запрос успешно обработан']]);

        $transaction->refresh();

        $this->assertTrue($transaction->isStatusProcessing());

        UnitPay::shouldReceive('request')
            ->once()
            ->andReturn(new UnitPayApiResponse(json_encode([
                'result' => [
                    'paymentId' => config('unitpay.project_id'),
                    'status' => 'success',
                    'paymentType' => 'webmoney',
                    'date' => now()->toDateTimeString(),
                    'purse' => "R123456789123",
                    'account' => $transaction->id,
                    'profit' => "4.93",
                    'projectId' => config('unitpay.project_id'),
                    'orderSum' => "5.00",
                    'orderCurrency' => "RUB",
                    'payerSum' => "5.00",
                    'payerCurrency' => "RUB",
                    'availableForRefund' => '',
                    'refunds' => []
                ]
            ])));
        UnitPay::makePartial();

        $this->json('get', route('unitpay.handler'),
            [
                'method' => 'pay',
                'params' => array_merge($params, ['signature' => UnitPay::getSignature('pay', $params)])
            ])
            ->assertJson(['result' => ['message' => 'Запрос успешно обработан']]);

        $transaction->refresh();

        $this->assertTrue($transaction->isStatusSuccess());

        $buyer->refresh();

        $this->assertEquals($sum, $buyer->balance);
        $this->assertEquals(0, $buyer->frozen_balance());
        $this->assertEquals('webmoney', $transaction->operable->payment_type);
        $this->assertNotNull($transaction->operable->payment_id);
    }

    public function testWithdrawalIfTransactionReceiptExists()
    {
        $transaction = UserPaymentTransaction::factory()->receipt()->create();

        $user = $transaction->user;
        $user->group->withdrawal = false;
        $user->push();

        $this->assertFalse($transaction->user->can('withdrawal', $transaction->user));
    }

    public function testPaymentHandlerTransactionNotFound()
    {
        $params = [
            'unitpayId' => rand(10000, 1000000),
            'projectId' => config('unitpay.project_id'),
            'account' => '',
            'payerSum' => 10,
            'payerCurrency' => 'RUB',
            'profit' => 10,
            'paymentType' => 'card',
            'orderSum' => 10,
            'orderCurrency' => 'RUB',
            'date' => now()->toDateTimeString(),
            'test' => '0'
        ];

        $this->json('get', route('unitpay.handler'),
            [
                'method' => 'check',
                'params' => array_merge($params, ['signature' => UnitPay::getSignature('check', $params)])
            ])
            ->assertJson(['error' => ['message' => __('user_payment_transaction.transaction_not_found')]]);
    }

    public function testDepositRouteIsOk()
    {
        $user = User::factory()->create();
        $user->group->shop_enable = true;
        $user->push();

        $this->actingAs($user)
            ->get(route('users.wallet.deposit', ['user' => $user]))
            ->assertOk();
    }

    public function testDepositPayValidationPaymentTypeError()
    {
        $user = User::factory()->create();
        $user->group->shop_enable = true;
        $user->push();

        $response = $this->actingAs($user)
            ->post(route('users.wallet.deposit.pay', ['user' => $user]), [
                'sum' => '100',
            ])
            ->assertRedirect();

        $response->assertSessionHasErrors(['payment_type' => __('validation.required', ['attribute' => __('user_incoming_payment.payment_type')])]);
    }

    public function testTestQueryHandler()
    {
        $params = [
            'unitpayId' => rand(10000, 1000000),
            'projectId' => config('unitpay.project_id'),
            'account' => 'test',
            'payerSum' => '10.00',
            'payerCurrency' => 'RUB',
            'profit' => "9.4",
            'paymentType' => "cash",
            'orderSum' => "10.00",
            'orderCurrency' => 'RUB',
            'date' => now()->toDateTimeString(),
            'test' => '1',
            'ip' => '212.32.225.240',
            'operator' => "euroset",
            'sum' => '10'
        ];

        $this->json('get', route('unitpay.handler'),
            [
                'method' => 'check',
                'params' => array_merge($params, ['signature' => UnitPay::getSignature('check', $params)])
            ])
            ->assertJson(['result' => ['message' => 'Тестовый запрос успешно обработан']]);
    }

    public function testHandlerSendErrorButGetWaitTransactionResponse()
    {
        $transaction = UserPaymentTransaction::factory()->incoming()->processing()->unitpay()->create(['sum' => '50']);

        $buyer = $transaction->user;

        $json = [
            'result' => [
                'status' => 'wait',
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

        $params = [
            'unitpayId' => $transaction->operable->payment_id,
            'account' => $transaction->id,
            'payerSum' => $transaction->sum + 10,
            'payerCurrency' => 'RUB',
            'profit' => $transaction->sum,
            'paymentType' => $transaction->operable->payment_type,
            'orderSum' => $transaction->sum,
            'orderCurrency' => 'RUB',
            'date' => now()->toDateTimeString(),
            'test' => '0',
            "errorMessage" => "text_error",
            "ip" => "11.12.32.42",
            "isPreauth" => "0",
            "operator" => "card",
            "purse" => "481776xxxxxx5555",
            "sum" => "99",
        ];

        $this->json('get', route('unitpay.handler'),
            [
                'method' => 'error',
                'params' => array_merge($params, ['signature' => UnitPay::getSignature('error', $params)])
            ])
            ->assertJson([
                'error' => [
                    'message' => 'Запрос послан типа error, но при получении данных о транзакции получен статус wait',
                    'code' => 0
                ]
            ]);

        $transaction->refresh();

        $this->assertEquals($json, $transaction->operable->getParamsArray());

        $this->assertTrue($transaction->isStatusError());
    }

    public function testCanPayErrorTransaction()
    {
        $transaction = UserPaymentTransaction::factory()->incoming()->error()->unitpay()->create(['sum' => '50']);

        $this->assertTrue($transaction->user->can('pay', $transaction));
    }

    public function testTodayProfit()
    {
        $transaction = UserPaymentTransaction::factory()->incoming()->success()->unitpay()->create(['sum' => '50']);

        $user = $transaction->user;

        $this->assertEquals(50, $user->today_profit());

        Carbon::setTestNow(now()->addHours(23));

        $this->assertEquals(50, $user->today_profit());

        Carbon::setTestNow(now()->addHours(2));

        $this->assertEquals(0, $user->today_profit());
    }

    public function testMonthProfit()
    {
        $transaction = UserPaymentTransaction::factory()->incoming()->success()->unitpay()->create(['sum' => '50']);

        $user = $transaction->user;

        $this->assertEquals(50, $user->month_profit());

        Carbon::setTestNow(now()->addDays(25));

        $this->assertEquals(50, $user->month_profit());

        Carbon::setTestNow(now()->addDays(10));

        $this->assertEquals(0, $user->month_profit());
    }
}
