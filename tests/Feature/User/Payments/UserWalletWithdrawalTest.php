<?php

namespace Tests\Feature\User\Payments;

use App\Notifications\WithdrawalOrderedNotification;
use App\User;
use App\UserPaymentDetail;
use App\UserPaymentTransaction;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class UserWalletWithdrawalTest extends TestCase
{
    public function testCreate()
    {
        Notification::fake();

        $user = User::factory()->withSelledBook(1000)->create();
        $user->group->withdrawal = true;
        $user->push();

        $wallet = UserPaymentDetail::factory()->create(['user_id' => $user->id])
            ->fresh();

        config(['litlife.min_outgoing_payment_sum' => 500]);

        $response = $this->actingAs($user)
            ->followingRedirects()
            ->post(route('users.wallet.withdrawal.save', ['user' => $user]),
                [
                    'wallet' => $wallet->id,
                    'sum' => 600
                ], ['REMOTE_ADDR' => '1.2.3.4'])
            ->assertOk()
            ->assertSeeText(__('user_outgoing_payment.payment_created'));

        $user->refresh();

        $payment = $user->outgoing_payment()->first();

        $this->assertEquals($wallet->type, $payment->payment_type);
        $this->assertEquals($wallet->number, $payment->purse);
        $this->assertEquals('1.2.3.4', $payment->ip);
        $this->assertEquals($user->id, $payment->user_id);

        $transaction = $payment->transaction;

        $this->assertEquals(-600, $transaction->sum);
        $this->assertEquals($user->id, $transaction->user_id);

        $this->assertEquals(400, $user->balance);
        $this->assertEquals($wallet->id, $payment->wallet_id);

        $this->assertTrue($transaction->isStatusWait());

        Notification::assertSentTo(
            $user,
            WithdrawalOrderedNotification::class,
            function ($notification, $channels) use ($payment) {
                $this->assertContains('mail', $channels);

                $mail = $notification->toMail($payment->transaction->user);

                $this->assertEquals(__('notification.withdrawal_ordered.subject'), $mail->subject);

                $this->assertEquals(__('notification.withdrawal_ordered.line', [
                    'sum' => abs($payment->transaction->sum) - $payment->getPayoutComission(),
                    'comission' => $payment->getPayoutComission()
                ]), $mail->introLines[0]);

                $this->assertEquals(__('notification.withdrawal_ordered.line2', [
                    'payment_type' => __('user_payment_transaction.payment_types_array.'.$payment->payment_type),
                    'purse' => $payment->purse
                ]), $mail->introLines[1]);

                $this->assertEquals(__('notification.withdrawal_ordered.line3', [
                    'transaction_id' => $payment->transaction->id
                ]), $mail->introLines[2]);

                $this->assertEquals(__('notification.withdrawal_ordered.line4'), $mail->introLines[3]);

                $this->assertEquals(4, count($mail->introLines));

                $this->assertEquals(__('notification.withdrawal_ordered.action'), $mail->actionText);
                $this->assertEquals(route('users.wallet', ['user' => $payment->transaction->user]), $mail->actionUrl);

                return $notification->payment->id == $payment->id;
            }
        );
    }

    public function testWithdrawalPageHttp()
    {
        $user = User::factory()->withSelledBook(1000)->create();
        $user->group->withdrawal = true;
        $user->push();

        $response = $this->actingAs($user)
            ->get(route('users.wallet.withdrawal', ['user' => $user]))
            ->assertOk();
    }

    public function testWithdrawalOtherUserForbiddenHttp()
    {
        $user = User::factory()->withMoneyOnBalance()->create();
        $user->group->withdrawal = true;
        $user->push();

        $user2 = User::factory()->create();
        $user2->group->withdrawal = true;
        $user2->push();

        $response = $this->actingAs($user2)
            ->get(route('users.wallet.withdrawal', ['user' => $user]))
            ->assertForbidden();
    }

    public function testMinimumSum()
    {
        $user = User::factory()->withSelledBook(1000)->create();
        $user->group->withdrawal = true;
        $user->push();

        $wallet = UserPaymentDetail::factory()->create(['user_id' => $user->id])
            ->fresh();

        config(['litlife.min_outgoing_payment_sum' => 1000]);

        $response = $this->actingAs($user)
            ->post(route('users.wallet.withdrawal.save', ['user' => $user]),
                [
                    'wallet' => $wallet->id,
                    'sum' => 600
                ])
            ->assertRedirect()
            ->assertSessionHasErrors(['sum' => __('user_outgoing_payment.sum_can_not_be_less_than_the_minimum')]);
    }

    public function testWithdrawalSaveHttp()
    {
        $user = User::factory()->withMoneyOnBalance()->create();
        $user->group->withdrawal = true;
        $user->push();

        $wallet = UserPaymentDetail::factory()->create(['user_id' => $user->id])
            ->fresh();

        config(['litlife.min_outgoing_payment_sum' => 100]);

        $user2 = User::factory()->create();
        $user2->group->withdrawal = true;
        $user2->push();

        $response = $this->actingAs($user2)
            ->followingRedirects()
            ->post(route('users.wallet.withdrawal.save', ['user' => $user]),
                [
                    'wallet' => $wallet->id,
                    'sum' => 200
                ])
            ->assertForbidden();
    }

    public function testRuCardWithdrawalRestriction()
    {
        $min = rand(20, 100);

        config(['unitpay.withdrawal_restrictions.card_rf.min' => $min]);
        config(['unitpay.withdrawal_restrictions.card.min' => '10']);

        $wallet = UserPaymentDetail::factory()->card()->ru_card()->create()
            ->fresh();

        $response = $this->actingAs($wallet->user)
            ->post(route('users.wallet.withdrawal.save', ['user' => $wallet->user]),
                [
                    'wallet' => $wallet->id,
                    'sum' => 10
                ])
            ->assertRedirect();
//dump(session('errors'));
        $response->assertSessionHasErrors(['sum' => __('validation.min.numeric', ['attribute' => __('user_outgoing_payment.sum'), 'min' => $min])]);
    }

    public function testWebmoneyWithdrawalRestriction()
    {
        $min = rand(100, 200);

        config(['unitpay.withdrawal_restrictions.webmoney.min' => $min]);

        $wallet = UserPaymentDetail::factory()->webmoney()->create()
            ->fresh();

        $response = $this->actingAs($wallet->user)
            ->post(route('users.wallet.withdrawal.save', ['user' => $wallet->user]),
                [
                    'wallet' => $wallet->id,
                    'sum' => 10
                ])
            ->assertRedirect();
//dump(session('errors'));
        $response->assertSessionHasErrors(['sum' => __('validation.min.numeric', ['attribute' => __('user_outgoing_payment.sum'), 'min' => $min])]);
    }

    public function testYandexWithdrawalRestriction()
    {
        $max = rand(100, 200);

        config(['unitpay.withdrawal_restrictions.yandex.max' => $max]);

        $wallet = UserPaymentDetail::factory()->yandex()->create()
            ->fresh();

        $response = $this->actingAs($wallet->user)
            ->post(route('users.wallet.withdrawal.save', ['user' => $wallet->user]),
                [
                    'wallet' => $wallet->id,
                    'sum' => 300
                ])
            ->assertRedirect();
//dump(session('errors'));
        $response->assertSessionHasErrors(['sum' => __('validation.max.numeric', ['attribute' => __('user_outgoing_payment.sum'), 'max' => $max])]);
    }

    public function testDidntChoseAnyWallet()
    {
        $max = rand(100, 200);

        config(['unitpay.withdrawal_restrictions.yandex.max' => $max]);

        $wallet = UserPaymentDetail::factory()->yandex()->create()
            ->fresh();

        $response = $this->actingAs($wallet->user)
            ->post(route('users.wallet.withdrawal.save', ['user' => $wallet->user]))
            ->assertRedirect()
            ->assertSessionHasErrors([
                'wallet' => __('validation.required', ['attribute' => __('user_outgoing_payment.wallet')]),
                'sum' => __('validation.required', ['attribute' => __('user_outgoing_payment.sum')]),
            ]);
    }

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testSumCannotExceedAvailableBalance()
    {
        $wallet = UserPaymentDetail::factory()->create()->fresh();

        $user = $wallet->user;
        $user->group->withdrawal = true;
        $user->push();

        $transaction = UserPaymentTransaction::factory()
            ->receipt()
            ->create([
                'user_id' => $user->id,
                'sum' => 100
            ])->fresh();

        $user->refresh();

        $response = $this->actingAs($user)
            ->post(route('users.wallet.withdrawal.save', ['user' => $user]),
                [
                    'wallet' => $wallet->id,
                    'sum' => 200
                ])
            ->assertRedirect();
        //dump(session('errors'));
        $response->assertSessionHasErrors(['sum' => __('user_outgoing_payment.sum_cannot_exceed_the_available_balance')]);
    }

    public function testWalletNotFound()
    {
        $wallet = UserPaymentDetail::factory()->create()->fresh();

        $wallet2 = UserPaymentDetail::factory()->create()->fresh();
        $user = $wallet2->user;
        $user->group->withdrawal = true;
        $user->push();

        $transaction = UserPaymentTransaction::factory()
            ->receipt()
            ->create([
                'user_id' => $user->id,
                'sum' => 200
            ])->fresh();

        $user->refresh();

        $response = $this->actingAs($user)
            ->post(route('users.wallet.withdrawal.save', ['user' => $user]),
                [
                    'wallet' => $wallet->id,
                    'sum' => 200
                ])
            ->assertRedirect();
        //dump(session('errors'));
        $response->assertSessionHasErrors(['wallet' => __('user_outgoing_payment.wallet_not_found')]);
    }
}
