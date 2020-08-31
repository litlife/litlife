<?php

/* @var $factory Factory */

use App\Enums\PaymentStatusEnum;
use App\User;
use App\UserIncomingPayment;
use App\UserMoneyTransfer;
use App\UserOutgoingPayment;
use App\UserPaymentTransaction;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

$factory->define(UserPaymentTransaction::class, function (Faker $faker) {

	$sum = rand(-100, 100);

	return [
		'user_id' => function () {
			return factory(User::class)
				->create()->id;
		},
		'sum' => $sum,
		'type' => 0,
		'params' => [],
		'status' => PaymentStatusEnum::Success,
		'status_changed_at' => now()
	];
});

$factory->afterMakingState(App\UserPaymentTransaction::class, 'incoming', function (UserPaymentTransaction $transaction, $faker) {

	$payment = factory(UserIncomingPayment::class)
		->create(['user_id' => $transaction->user_id]);

	$transaction->operable_type = 14;
	$transaction->operable_id = $payment->id;
	$transaction->typeDeposit();
});

$factory->afterMakingState(App\UserPaymentTransaction::class, 'outgoing', function (UserPaymentTransaction $transaction, $faker) {

	$payment = factory(UserOutgoingPayment::class)
		->create(['user_id' => $transaction->user_id]);

	$transaction->operable_type = 15;
	$transaction->operable_id = $payment->id;
	$transaction->typeWithdrawal();
	$transaction->sum = -abs($transaction->sum);
});

$factory->afterMakingState(App\UserPaymentTransaction::class, 'success', function (UserPaymentTransaction $transaction, $faker) {
	$transaction->statusSuccess();
});

$factory->afterMakingState(App\UserPaymentTransaction::class, 'wait', function (UserPaymentTransaction $transaction, $faker) {
	$transaction->statusWait();
});

$factory->afterMakingState(App\UserPaymentTransaction::class, 'processing', function (UserPaymentTransaction $transaction, $faker) {
	$transaction->statusProcessing();
});

$factory->afterMakingState(App\UserPaymentTransaction::class, 'canceled', function (UserPaymentTransaction $transaction, $faker) {
	$transaction->statusCanceled();
});

$factory->afterMakingState(App\UserPaymentTransaction::class, 'error', function (UserPaymentTransaction $transaction, $faker) {
	$transaction->statusError();
});

$factory->afterCreating(App\UserPaymentTransaction::class, function (UserPaymentTransaction $transaction, $faker) {
	$transaction->user->balance(true);
	$transaction->user->frozen_balance(true);
});

$factory->afterMakingState(App\UserPaymentTransaction::class, 'unitpay', function (UserPaymentTransaction $transaction, $faker) {


});

$factory->afterCreatingState(App\UserPaymentTransaction::class, 'unitpay', function (UserPaymentTransaction $transaction, $faker) {

	if ($transaction->isDeposit()) {

		if ($transaction->isStatusSuccess()) {
			$payment_id = rand(1000, 100000);

			$transaction->operable->params = [
				'result' => [
					'paymentId' => $payment_id,
					'projectId' => config('unitpay.project_id'),
					'account' => $transaction->id,
					'payerSum' => $transaction->sum + 10,
					'payerCurrency' => 'RUB',
					'profit' => $transaction->sum,
					'paymentType' => $transaction->operable->payment_type,
					'orderSum' => $transaction->sum,
					'orderCurrency' => 'RUB',
					'date' => now()->toDateTimeString(),
					'purse' => '1234123412341234',
					'test' => '0'
				]
			];
			$transaction->operable->save();
		}

		if ($transaction->isStatusProcessing()) {
			$payment_id = rand(1000, 100000);

			$transaction->operable->params = [
				'result' => [
					'paymentId' => $payment_id,
					'projectId' => config('unitpay.project_id'),
					'account' => $transaction->id,
					'payerSum' => $transaction->sum + 10,
					'payerCurrency' => 'RUB',
					'profit' => $transaction->sum,
					'paymentType' => $transaction->operable->payment_type,
					'orderSum' => $transaction->sum,
					'orderCurrency' => 'RUB',
					'date' => now()->toDateTimeString()
				]
			];
		}

		if ($transaction->isStatusError()) {
			$payment_id = rand(1000, 100000);

			$transaction->operable->params = [
				'result' => [
					'paymentId' => $payment_id,
					'projectId' => config('unitpay.project_id'),
					'account' => $transaction->id,
					'payerSum' => $transaction->sum + 10,
					'payerCurrency' => 'RUB',
					'profit' => $transaction->sum,
					'paymentType' => $transaction->operable->payment_type,
					'orderSum' => $transaction->sum,
					'orderCurrency' => 'RUB',
					'date' => now()->toDateTimeString()
				]
			];
		}

		if (!empty($transaction->operable->getParamsArray()['result']['paymentId'])) {
			$transaction->operable->payment_id = $transaction->operable->getParamsArray()['result']['paymentId'];
		}

		$transaction->sum = abs($transaction->sum);
	}

	if ($transaction->isWithdrawal()) {
		$transaction->sum = -abs($transaction->sum);

		if ($transaction->isStatusSuccess()) {
			$payment_id = rand(1000, 100000);

			$transaction->operable->params = [
				'result' => [
					'message' => 'Выплата произведена',
					'payoutId' => rand(10000, 1000000000),
					'status' => 'success',
					'partnerBalance' => '1000.10',
					'payoutCommission' => '0.45',
					"partnerCommission" => "0.00",
					'sum' => $transaction->sum,
					'createDate' => '2019-08-26 13:30:54',
					'completeDate' => '2019-08-26 13:30:55',
					'transactionId' => uniqid()
				]
			];
			$transaction->operable->save();
		} elseif ($transaction->isStatusProcessing()) {
			$payment_id = rand(1000, 100000);

			$transaction->operable->params = [
				'result' => [
					'message' => 'Выплата произведена',
					'payoutId' => rand(10000, 1000000000),
					'status' => 'not_completed',
					'partnerBalance' => '1000.10',
					'payoutCommission' => '0.45',
					"partnerCommission" => "0.00",
					'sum' => $transaction->sum,
					'createDate' => '2019-08-26 13:30:54',
					'completeDate' => '2019-08-26 13:30:55',
					'transactionId' => uniqid()
				]
			];
			$transaction->operable->save();
		}
	}

	$transaction->operable->save();
	$transaction->save();
});


$factory->afterMakingState(App\UserPaymentTransaction::class, 'transfer', function (UserPaymentTransaction $transaction, $faker) {

	$transfer = factory(UserMoneyTransfer::class)
		->create(['sender_user_id' => $transaction->user_id]);

	$transaction->sum = -abs($transaction->sum);
	$transaction->operable_type = 17;
	$transaction->operable_id = $transfer->id;
	$transaction->typeTransfer();
});

$factory->afterMakingState(App\UserPaymentTransaction::class, 'receipt', function (UserPaymentTransaction $transaction, $faker) {

	$transfer = factory(UserMoneyTransfer::class)
		->create(['recepient_user_id' => $transaction->user_id]);

	$transaction->sum = abs($transaction->sum);
	$transaction->operable_type = 17;
	$transaction->operable_id = $transfer->id;
	$transaction->typeReceipt();
});

