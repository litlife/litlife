<?php

namespace Tests\Feature\User\Payments;

use App\Author;
use App\Jobs\OutgoingPaymentJob;
use App\Jobs\UpdateOutgoingPaymentStatusJob;
use App\Notifications\WithdrawalOrderedNotification;
use App\Notifications\WithdrawalSuccessNotification;
use App\User;
use App\UserIncomingPayment;
use App\UserOutgoingPayment;
use App\UserPaymentDetail;
use App\UserPaymentTransaction;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Notification;
use Litlife\Unitpay\Facades\UnitPay;
use Litlife\Unitpay\UnitPayApiResponse;
use Litlife\Unitpay\UnitPayFake;
use Tests\TestCase;

class UserOutgoingPaymentTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        UserOutgoingPayment::truncate();
        UserIncomingPayment::truncate();
        UserPaymentTransaction::truncate();
    }

    public function testOutgoingPaymentJobStatusNotComlete()
    {
        $payment = UserOutgoingPayment::factory()->wait()->create();

        $this->assertTrue($payment->transaction->isStatusWait());

        $json = [
            'result' => [
                'message' => 'Выплата отправлена в платежную систему, но еще не получено подтверждение',
                'status' => 'not_completed',
                'payoutId' => rand(1000, 1000000),
                'partnerBalance' => '15733.00',
                'createDate' => now()->toDateTimeString(),
                'sum' => '1000',
                'payoutCommission' => '6.00',
                'partnerCommission' => '0',
            ]
        ];

        UnitPay::shouldReceive('request')
            ->once()
            ->andReturn(new UnitPayApiResponse(json_encode($json)));

        UnitPay::makePartial();

        dispatch(new OutgoingPaymentJob($payment));

        $payment->refresh();

        $this->assertEquals($payment->uniqid, UnitPay::getParams()['transactionId']);
        $this->assertTrue($payment->transaction->isStatusProcessing());
        $this->assertEquals($json, $payment->getParamsArray());
        $this->assertEquals('unitpay', $payment->payment_aggregator);
    }

    public function testOutgoingPaymentJobStatusComlete()
    {
        Notification::fake();

        $payment = UserOutgoingPayment::factory()->wait()->create();

        $json = [
            'result' => [
                'message' => 'Выплата успешно проведена',
                'status' => 'success',
                'payoutId' => rand(1000, 1000000),
                'partnerBalance' => '15733.00',
                'createDate' => now()->toDateTimeString(),
                'completeDate' => now()->toDateTimeString(),
                'sum' => '1000',
                'payoutCommission' => '6.00',
                'partnerCommission' => '0',
            ]
        ];

        UnitPay::shouldReceive('request')
            ->once()
            ->andReturn(new UnitPayApiResponse(json_encode($json)));
        UnitPay::makePartial();

        dispatch(new OutgoingPaymentJob($payment));

        $payment->refresh();

        $this->assertEquals($payment->uniqid, UnitPay::getParams()['transactionId']);
        $this->assertTrue($payment->transaction->isStatusSuccess());
        $this->assertEquals($json, $payment->getParamsArray());
        $this->assertEquals('unitpay', $payment->payment_aggregator);

        $this->assertNotificationSuccessPayment($payment);
    }

    public function assertNotificationSuccessPayment($payment)
    {
        Notification::assertSentTo(
            $payment->transaction->user,
            WithdrawalSuccessNotification::class,
            function ($notification, $channels) use ($payment) {
                $this->assertContains('mail', $channels);

                $mail = $notification->toMail($payment->transaction->user);

                $this->assertEquals(__('notification.withdrawal_success.subject'), $mail->subject);

                $this->assertEquals(__('notification.withdrawal_success.line', [
                    'sum' => abs($payment->transaction->sum) - $payment->getPayoutComission(),
                    'comission' => $payment->getPayoutComission()
                ]), $mail->introLines[0]);

                $this->assertEquals(__('notification.withdrawal_success.line2', [
                    'payment_type' => __('user_payment_transaction.payment_types_array.'.$payment->payment_type),
                    'purse' => $payment->purse
                ]), $mail->introLines[1]);

                $this->assertEquals(__('notification.withdrawal_success.line3', [
                    'transaction_id' => $payment->transaction->id
                ]), $mail->introLines[2]);

                $this->assertEquals(3, count($mail->introLines));

                $this->assertEquals(__('notification.withdrawal_success.action'), $mail->actionText);
                $this->assertEquals(route('users.wallet', ['user' => $payment->transaction->user]), $mail->actionUrl);

                return $notification->payment->id == $payment->id;
            }
        );
    }

    public function testHandleWaitedPayments()
    {
        Notification::fake();

        $payment = UserOutgoingPayment::factory()->wait()->create();

        $json = [
            'result' => [
                'message' => 'Выплата успешно проведена',
                'status' => 'success',
                'payoutId' => rand(1000, 1000000),
                'partnerBalance' => '15733.00',
                'createDate' => now()->toDateTimeString(),
                'completeDate' => now()->toDateTimeString(),
                'sum' => '1000',
                'payoutCommission' => '6.00',
                'partnerCommission' => '0',
            ]
        ];

        UnitPay::shouldReceive('request')
            ->once()
            ->andReturn(new UnitPayApiResponse(json_encode($json)));
        UnitPay::makePartial();

        Artisan::call('payments:handle_waited_outgoing');

        $payment->refresh();

        $this->assertEquals($payment->uniqid, UnitPay::getParams()['transactionId']);
        $this->assertTrue($payment->transaction->isStatusSuccess());
        $this->assertEquals($json, $payment->getParamsArray());
        $this->assertEquals('unitpay', $payment->payment_aggregator);

        $this->assertNotificationSuccessPayment($payment);
    }

    public function testUpdateOutgoingPaymentStatus()
    {
        Notification::fake();

        $user = User::factory()->withMoneyOnBalance()->create();

        $payment = UserPaymentTransaction::factory()
            ->outgoing()
            ->processing()
            ->create([
                'user_id' => $user->id,
                'sum' => 90
            ]);

        $this->assertEquals('910.00', $payment->user->balance());
        $this->assertEquals('90', $payment->user->frozen_balance());

        $json = [
            'result' => [
                'message' => 'Выплата успешно проведена',
                'status' => 'success',
                'payoutId' => rand(1000, 1000000),
                'partnerBalance' => '15733.00',
                'createDate' => now()->toDateTimeString(),
                'completeDate' => now()->toDateTimeString(),
                'sum' => '1000',
                'payoutCommission' => '6.00',
                'partnerCommission' => '0',
            ]
        ];

        UnitPay::shouldReceive('request')
            ->once()
            ->andReturn(new UnitPayApiResponse(json_encode($json)));
        UnitPay::makePartial();

        Artisan::call('payments:handle_processing_outgoing');

        $payment->refresh();
        $user->refresh();

        $this->assertEquals($payment->operable->uniqid, UnitPay::getParams()['transactionId']);
        $this->assertTrue($payment->isStatusSuccess());
        $this->assertEquals($json, $payment->operable->getParamsArray());
        $this->assertEquals('unitpay', $payment->operable->payment_aggregator);

        $this->assertNotificationSuccessPayment($payment->operable);

        $this->assertEquals('910', $payment->user->balance());
        $this->assertEquals('0', $payment->user->frozen_balance());
    }

    public function testUpdateErrorOutgoingPaymentStatus()
    {
        Notification::fake();

        $payment = UserOutgoingPayment::factory()->error()->create();

        $this->assertTrue($payment->transaction->isStatusError());

        $json = [
            'result' => [
                'message' => 'Выплата успешно проведена',
                'status' => 'success',
                'payoutId' => rand(1000, 1000000),
                'partnerBalance' => '15733.00',
                'createDate' => now()->toDateTimeString(),
                'completeDate' => now()->toDateTimeString(),
                'sum' => '1000',
                'payoutCommission' => '6.00',
                'partnerCommission' => '0',
            ]
        ];

        UnitPay::shouldReceive('request')
            ->once()
            ->andReturn(new UnitPayApiResponse(json_encode($json)));
        UnitPay::makePartial();

        Artisan::call('payments:handle_error_outgoing_payments');

        $payment->refresh();

        $this->assertEquals($payment->uniqid, UnitPay::getParams()['transactionId']);
        $this->assertTrue($payment->transaction->isStatusSuccess());
        $this->assertEquals($json, $payment->getParamsArray());
        $this->assertEquals('unitpay', $payment->payment_aggregator);

        $this->assertNotificationSuccessPayment($payment);
    }

    public function testUpdateOutgoingPaymentStatusJobWithError()
    {
        Notification::fake();

        $payment = UserOutgoingPayment::factory()->processing()->create();

        $json = [
            'error' => [
                'message' => __('user_outgoing_payment.errors.103'),
                'code' => 103
            ]
        ];

        UnitPay::shouldReceive('request')
            ->once()
            ->andReturn(new UnitPayApiResponse(json_encode($json)));
        UnitPay::makePartial();

        dispatch(new UpdateOutgoingPaymentStatusJob($payment->transaction));

        $payment->refresh();

        $this->assertEquals($payment->uniqid, UnitPay::getParams()['transactionId']);
        $this->assertTrue($payment->transaction->isStatusError());
        $this->assertEquals($json, $payment->getParamsArray());
        $this->assertEquals('unitpay', $payment->payment_aggregator);

        Notification::assertNothingSent();
    }

    public function testSetWmrPaymentType()
    {
        $payment = UserOutgoingPayment::factory()->processing()->create(['payment_type' => 'wmr']);

        $this->assertEquals('webmoney', $payment->payment_type);
    }

    public function testCancelWaitedOutgoingPayment()
    {
        $user = User::factory()
            ->withMoneyOnBalance()
            ->create();

        $transaction = UserPaymentTransaction::factory()
            ->outgoing()
            ->wait()
            ->create([
                'user_id' => $user->id,
                'sum' => -300
            ]);

        $user->refresh();

        $this->assertTrue($transaction->isWithdrawal());
        $this->assertTrue($transaction->isStatusWait());
        $this->assertEquals(700, $user->balance);
        $this->assertEquals(300, $user->frozen_balance());

        $this->actingAs($user)
            ->get(route('users.transaction.cancel', ['user' => $user, 'transaction' => $transaction]))
            ->assertRedirect();

        $transaction->refresh();
        $user->refresh();

        $this->assertTrue($transaction->isStatusCanceled());

        $this->assertEquals(0, $user->frozen_balance());
        $this->assertEquals(1000, $user->balance);
    }

    public function testCancelProcessingOutgoingPayment()
    {
        $user = User::factory()
            ->withMoneyOnBalance()
            ->create();

        $transaction = UserPaymentTransaction::factory()
            ->outgoing()
            ->processing()
            ->create([
                'user_id' => $user->id,
                'sum' => -300
            ]);

        $user->refresh();

        $this->assertTrue($transaction->isStatusProcessing());
        $this->assertEquals(300, $user->frozen_balance());
        $this->assertEquals(700, $user->balance);

        $this->actingAs($user)
            ->get(route('users.transaction.cancel', ['user' => $user, 'transaction' => $transaction]))
            ->assertNotFound();

        $transaction->refresh();
        $user->refresh();

        $this->assertTrue($transaction->isStatusProcessing());

        $this->assertEquals(300, $user->frozen_balance());
        $this->assertEquals(700, $user->balance);
    }

    public function testWithdrawalPolicy()
    {
        $user = User::factory()->create();

        $this->assertFalse($user->can('withdrawal', $user));

        $author = Author::factory()->with_author_manager()->create();

        $user = $author->managers->first()->user;

        $this->assertFalse($user->can('withdrawal', $user));

        $author = Author::factory()->with_author_manager_can_sell()->create();

        $user = $author->managers->first()->user;

        $this->assertFalse($user->can('withdrawal', $user));

        $user = User::factory()->create();
        $user->group->withdrawal = true;
        $user->push();

        $this->assertFalse($user->can('withdrawal', $user));

        $author = Author::factory()->with_author_manager_can_sell()->create();

        $user = $author->managers->first()->user;
        $user->group->withdrawal = true;

        $this->assertTrue($user->can('withdrawal', $user));
    }

    public function testHandleWaitedToError()
    {
        $payment = UserOutgoingPayment::factory()->wait()->create();

        $json = [
            'error' => [
                'message' => 'Мы не смогли получить информацию о номере карты. Проверьте номер карты и попробуйте повторить операцию снова или через некоторое время',
                'code' => '1052'
            ]
        ];

        UnitPay::shouldReceive('request')
            ->once()
            ->andReturn(new UnitPayApiResponse(json_encode($json)));
        UnitPay::makePartial();

        Artisan::call('payments:handle_waited_outgoing');

        $payment->refresh();

        $this->assertEquals($payment->uniqid, UnitPay::getParams()['transactionId']);
        $this->assertTrue($payment->transaction->isStatusError());
        $this->assertEquals($json, $payment->getParamsArray());
        $this->assertEquals('unitpay', $payment->payment_aggregator);
        $this->assertEquals(1, $payment->retry_failed_count);
        $this->assertNotNull($payment->last_failed_retry_at);

        $json = [
            'error' => [
                'message' => 'Мы не смогли получить информацию о номере карты. Проверьте номер карты и попробуйте повторить операцию снова или через некоторое время',
                'code' => '1052'
            ]
        ];

        UnitPay::shouldReceive('request')
            ->once()
            ->andReturn(new UnitPayApiResponse(json_encode($json)));
        UnitPay::makePartial();

        Artisan::call('payments:handle_error_outgoing_payments');

        $payment->refresh();

        $this->assertEquals(2, $payment->retry_failed_count);
    }

    public function testCancelIfRetryFailedCount()
    {
        config(['litlife.max_outgoing_payment_retry_failed_count' => 3]);

        $user = User::factory()->withSelledBook(1000)->create();

        $payment = UserOutgoingPayment::factory()
            ->error()
            ->create([
                'user_id' => $user->id,
                'retry_failed_count' => config('litlife.max_outgoing_payment_retry_failed_count')
            ]);

        $this->assertEquals(abs($payment->transaction->sum), $user->frozen_balance());
        $this->assertEquals(1000 - abs($payment->transaction->sum), $user->balance(true));

        $json = [
            'error' => [
                'message' => 'Мы не смогли получить информацию о номере карты. Проверьте номер карты и попробуйте повторить операцию снова или через некоторое время',
                'code' => '1052'
            ]
        ];

        UnitPay::shouldReceive('request')
            ->once()
            ->andReturn(new UnitPayApiResponse(json_encode($json)));
        UnitPay::makePartial();

        Artisan::call('payments:handle_error_outgoing_payments');

        $payment->refresh();

        $this->assertEquals($payment->uniqid, UnitPay::getParams()['transactionId']);
        $this->assertTrue($payment->transaction->isStatusCanceled());
        $this->assertEquals($json, $payment->getParamsArray());
        $this->assertEquals('unitpay', $payment->payment_aggregator);
        $this->assertEquals((config('litlife.max_outgoing_payment_retry_failed_count') + 1), $payment->retry_failed_count);
        $this->assertNotNull($payment->last_failed_retry_at);

        $user->refresh();

        $this->assertEquals(1000, $user->balance());
        $this->assertEquals(0, $user->frozen_balance());
    }

    public function testSeeUnitPay1000ErrorHttp()
    {
        $transaction = UserPaymentTransaction::factory()->outgoing()->error()->unitpay()->create();

        $s = json_decode('{"error":{"message":"<b> \u0412\u043d\u0438\u043c\u0430\u043d\u0438\u0435!<\/b> \u041c\u0438\u043d\u0438\u043c\u0430\u043b\u044c\u043d\u0430\u044f \u0441\u0443\u043c\u043c\u0430 \u0435\u0434\u0438\u043d\u043e\u0432\u0440\u0435\u043c\u0435\u043d\u043d\u043e\u0433\u043e \u043f\u043b\u0430\u0442\u0435\u0436\u0430 \u0434\u043e\u043b\u0436\u043d\u0430 \u0431\u044b\u0442\u044c \u043d\u0435 \u043c\u0435\u043d\u0435\u0435 50 \u0440\u0443\u0431\u043b\u0435\u0439.","code":1000}}');

        $transaction->operable->params = $s;
        $transaction->push();

        $this->assertEquals(1000, $transaction->operable->getErrorCode());

        $this->actingAs($transaction->user)
            ->get(route('users.wallet', ['user' => $transaction->user]))
            ->assertOk()
            ->assertSeeText(strip_tags($transaction->operable->getPaymentError()));

    }

    public function testPayoutPartnerComission()
    {
        $transaction = UserPaymentTransaction::factory()->outgoing()->success()->unitpay()->create();

        $this->assertEquals('0.45', $transaction->operable->getPayoutComission());
        $this->assertEquals('0', $transaction->operable->getPartnerComission());

        $string = json_decode('{"result":{"message":"\u0412\u044b\u043f\u043b\u0430\u0442\u0430 \u0443\u0441\u043f\u0435\u0448\u043d\u043e \u043f\u0440\u043e\u0432\u0435\u0434\u0435\u043d\u0430","payoutId":193186721,"status":"success","partnerBalance":"246.40","payoutCommission":"0.65","partnerCommission":"0.00","sum":"30.00","createDate":"2019-08-26 13:30:54","completeDate":"2019-08-26 13:30:55","transactionId":"55cc4e3b-6f76-4f2c-9cba-1c7edc8a0808"}}');

        $transaction->operable->params = $string;
        $transaction->push();
        $transaction->refresh();

        $this->assertEquals('0.65', $transaction->operable->getPayoutComission());
        $this->assertEquals('0.00', $transaction->operable->getPartnerComission());
    }
}
