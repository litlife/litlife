<?php

/* @var $factory Factory */

use App\UserIncomingPayment;
use App\UserPaymentTransaction;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

$factory->define(UserIncomingPayment::class, function (Faker $faker) {

	$payment_types = ['mc', 'sms', 'card', 'webmoney', 'qiwi', 'paypal', 'liqpay', 'alfaClick', 'cash', 'applepay'];

	return [
		'payment_type' => $payment_types[array_rand($payment_types)],
		'user_id' => function () {
			return factory(App\User::class)->create()->id;
		},
		'ip' => $faker->ipv4,
		'currency' => 'RUB',
		'payment_aggregator' => 'unitpay',
		'params' => []
	];
});

$factory->afterCreatingState(App\UserIncomingPayment::class, 'wait', function ($payment, $faker) {

	$transaction = factory(UserPaymentTransaction::class)
		->make([
			'user_id' => $payment['user_id'],
			'sum' => rand(50, 100)
		]);
	$transaction->typeDeposit();
	$transaction->statusWait();

	$payment->transaction()->save($transaction);

	$payment->user->balance(true);
});

$factory->afterCreatingState(App\UserIncomingPayment::class, 'processing', function ($payment, $faker) {

	$payment->payment_id = rand(100, 1000000);
	$payment->save();

	$transaction = factory(UserPaymentTransaction::class)
		->make([
			'user_id' => $payment['user_id'],
			'sum' => rand(50, 100)
		]);

	$transaction->typeDeposit();
	$transaction->statusProcessing();
	$payment->transaction()->save($transaction);
});

$factory->afterCreatingState(App\UserIncomingPayment::class, 'unitpay_success', function ($payment, $faker) {

	$payment->payment_id = rand(100, 1000000);
	$payment->save();

	$transaction = factory(UserPaymentTransaction::class)
		->make([
			'user_id' => $payment['user_id'],
			'sum' => rand(50, 100)
		]);
	$transaction->typeDeposit();
	$transaction->statusSuccess();

	$payment->transaction()->save($transaction);

	$payment->params = ['result' => [
		'unitpayId' => $payment->payment_id,
		'projectId' => config('unitpay.project_id'),
		'account' => $payment->id,
		'payerSum' => $payment->sum + 10,
		'payerCurrency' => 'RUB',
		'profit' => $payment->sum,
		'paymentType' => $payment->payment_type,
		'orderSum' => $payment->sum,
		'orderCurrency' => 'RUB',
		'date' => now()->toDateTimeString(),
		'purse' => '1234123412341234',
		'test' => '0'
	]];
	$payment->save();

	$payment->user->balance(true);
});

$factory->afterCreatingState(App\UserIncomingPayment::class, 'error', function ($payment, $faker) {

	$payment->payment_id = rand(100, 1000000);
	$payment->save();

	$transaction = factory(UserPaymentTransaction::class)
		->make([
			'user_id' => $payment['user_id'],
			'sum' => rand(50, 100)
		]);
	$transaction->typeDeposit();
	$transaction->statusError();

	$payment->transaction()->save($transaction);
});

$factory->afterCreatingState(App\UserIncomingPayment::class, 'canceled', function ($payment, $faker) {

	$transaction = factory(UserPaymentTransaction::class)
		->make([
			'user_id' => $payment['user_id'],
			'sum' => rand(50, 100)
		]);

	$transaction->typeDeposit();
	$transaction->statusCanceled();

	$payment->transaction()->save($transaction);
});
