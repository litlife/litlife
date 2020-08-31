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

	/**
	 * A basic feature test example.
	 *
	 * @return void
	 */
	public function testSumCannotExceedAvailableBalance()
	{
		$wallet = factory(UserPaymentDetail::class)
			->create()->fresh();

		$user = $wallet->user;
		$user->group->withdrawal = true;
		$user->push();

		$transaction = factory(UserPaymentTransaction::class)
			->states('receipt')
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
		$wallet = factory(UserPaymentDetail::class)
			->create()->fresh();

		$wallet2 = factory(UserPaymentDetail::class)
			->create()->fresh();
		$user = $wallet2->user;
		$user->group->withdrawal = true;
		$user->push();

		$transaction = factory(UserPaymentTransaction::class)
			->states('receipt')
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

	public function testCreate()
	{
		Notification::fake();

		$user = factory(User::class)
			->states('with_thousand_earned_money_on_balance')
			->create();
		$user->group->withdrawal = true;
		$user->push();

		$wallet = factory(UserPaymentDetail::class)
			->create(['user_id' => $user->id])
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
					'payment_type' => __('user_payment_transaction.payment_types_array.' . $payment->payment_type),
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

	public function testMinimumSum()
	{
		$user = factory(User::class)
			->states('with_thousand_earned_money_on_balance')
			->create();
		$user->group->withdrawal = true;
		$user->push();

		$wallet = factory(UserPaymentDetail::class)
			->create(['user_id' => $user->id])
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

	public function testOutgoingPaymentJobStatusNotComlete()
	{
		$payment = factory(UserOutgoingPayment::class)
			->states('wait')
			->create();

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

		$payment = factory(UserOutgoingPayment::class)
			->states('wait')
			->create();

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
					'payment_type' => __('user_payment_transaction.payment_types_array.' . $payment->payment_type),
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

		$payment = factory(UserOutgoingPayment::class)
			->states('wait')
			->create();

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

		$user = factory(User::class)
			->states('with_thousand_money_on_balance')
			->create();

		$payment = factory(UserPaymentTransaction::class)
			->states(['outgoing', 'processing'])
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

		$payment = factory(UserOutgoingPayment::class)
			->states('error')
			->create();

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

		$payment = factory(UserOutgoingPayment::class)
			->states('processing')
			->create();

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
		$payment = factory(UserOutgoingPayment::class)
			->states('processing')
			->create(['payment_type' => 'wmr']);

		$this->assertEquals('webmoney', $payment->payment_type);
	}

	public function testCancelWaitedOutgoingPayment()
	{
		$user = factory(User::class)
			->states(['with_thousand_money_on_balance'])
			->create();

		$transaction = factory(UserPaymentTransaction::class)
			->states(['outgoing', 'wait'])
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
		$user = factory(User::class)
			->states(['with_thousand_money_on_balance'])
			->create();

		$transaction = factory(UserPaymentTransaction::class)
			->states(['outgoing', 'processing'])
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
		$user = factory(User::class)
			->create();

		$this->assertFalse($user->can('withdrawal', $user));

		$author = factory(Author::class)
			->states('with_author_manager')
			->create();

		$user = $author->managers->first()->user;

		$this->assertFalse($user->can('withdrawal', $user));

		$author = factory(Author::class)
			->states('with_author_manager_can_sell')
			->create();

		$user = $author->managers->first()->user;

		$this->assertFalse($user->can('withdrawal', $user));

		$user = factory(User::class)
			->create();
		$user->group->withdrawal = true;
		$user->push();

		$this->assertFalse($user->can('withdrawal', $user));

		$author = factory(Author::class)
			->states('with_author_manager_can_sell')
			->create();

		$user = $author->managers->first()->user;
		$user->group->withdrawal = true;

		$this->assertTrue($user->can('withdrawal', $user));
	}

	public function testHandleWaitedToError()
	{
		$payment = factory(UserOutgoingPayment::class)
			->states('wait')
			->create();

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

		$user = factory(User::class)
			->states('with_thousand_earned_money_on_balance')
			->create();

		$payment = factory(UserOutgoingPayment::class)
			->states('error')
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

	public function testRuCardWithdrawalRestriction()
	{
		$min = rand(20, 100);

		config(['unitpay.withdrawal_restrictions.card_rf.min' => $min]);
		config(['unitpay.withdrawal_restrictions.card.min' => '10']);

		$wallet = factory(UserPaymentDetail::class)
			->states('card', 'ru_card')
			->create()
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

		$wallet = factory(UserPaymentDetail::class)
			->states('webmoney')
			->create()
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

		$wallet = factory(UserPaymentDetail::class)
			->states('yandex')
			->create()
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

	public function testSeeUnitPay1000ErrorHttp()
	{
		$transaction = factory(UserPaymentTransaction::class)
			->states('outgoing', 'error', 'unitpay')
			->create();

		$s = json_decode('{"error":{"message":"<b> \u0412\u043d\u0438\u043c\u0430\u043d\u0438\u0435!<\/b> \u041c\u0438\u043d\u0438\u043c\u0430\u043b\u044c\u043d\u0430\u044f \u0441\u0443\u043c\u043c\u0430 \u0435\u0434\u0438\u043d\u043e\u0432\u0440\u0435\u043c\u0435\u043d\u043d\u043e\u0433\u043e \u043f\u043b\u0430\u0442\u0435\u0436\u0430 \u0434\u043e\u043b\u0436\u043d\u0430 \u0431\u044b\u0442\u044c \u043d\u0435 \u043c\u0435\u043d\u0435\u0435 50 \u0440\u0443\u0431\u043b\u0435\u0439.","code":1000}}');

		$transaction->operable->params = $s;
		$transaction->push();

		$this->assertEquals(1000, $transaction->operable->getErrorCode());

		$this->actingAs($transaction->user)
			->get(route('users.wallet', ['user' => $transaction->user]))
			->assertOk()
			->assertSeeText(strip_tags($transaction->operable->getPaymentError()));

	}

	public function testWithdrawalPageHttp()
	{
		$user = factory(User::class)
			->states('with_thousand_earned_money_on_balance')
			->create();
		$user->group->withdrawal = true;
		$user->push();

		$response = $this->actingAs($user)
			->get(route('users.wallet.withdrawal', ['user' => $user]))
			->assertOk();
	}

	public function testWithdrawalOtherUserForbiddenHttp()
	{
		$user = factory(User::class)
			->states('with_thousand_money_on_balance')
			->create();
		$user->group->withdrawal = true;
		$user->push();

		$user2 = factory(User::class)->create();
		$user2->group->withdrawal = true;
		$user2->push();

		$response = $this->actingAs($user2)
			->get(route('users.wallet.withdrawal', ['user' => $user]))
			->assertForbidden();
	}

	public function testWithdrawalSaveHttp()
	{
		$user = factory(User::class)
			->states('with_thousand_money_on_balance')
			->create();
		$user->group->withdrawal = true;
		$user->push();

		$wallet = factory(UserPaymentDetail::class)
			->create(['user_id' => $user->id])
			->fresh();

		config(['litlife.min_outgoing_payment_sum' => 100]);

		$user2 = factory(User::class)->create();
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

	public function testPayoutPartnerComission()
	{
		$transaction = factory(UserPaymentTransaction::class)
			->states('outgoing', 'success', 'unitpay')
			->create();

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
