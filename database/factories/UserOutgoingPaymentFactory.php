<?php

/* @var $factory Factory */

use App\UserOutgoingPayment;
use App\UserPaymentDetail;
use App\UserPaymentTransaction;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

$factory->define(UserOutgoingPayment::class, function (Faker $faker) {

    $payment_types = config('unitpay.allowed_outgoing_payment_types');

    return [
        'user_id' => function () {
            return factory(App\User::class)->create()->id;
        },
        'ip' => $faker->ipv4,
        'purse' => $faker->creditCardNumber,
        'payment_type' => $payment_types[array_rand($payment_types)],
        'wallet_id' => function (array $payment) {
            return factory(UserPaymentDetail::class)
                ->create([
                    'user_id' => $payment['user_id'],
                    'type' => $payment['payment_type'],
                    'number' => $payment['purse']
                ])->fresh()->id;
        },
        'payment_aggregator' => 'unitpay',
        'payment_aggregator_transaction_id' => null,
        'params' => null
    ];
});

$factory->afterCreatingState(App\UserOutgoingPayment::class, 'success', function ($payment, $faker) {
    $payment->payment_aggregator_transaction_id = $faker->randomNumber(5);
    $payment->save();

    $transaction = factory(UserPaymentTransaction::class)
        ->make([
            'user_id' => $payment->user_id,
            'sum' => -rand(50, 100)
        ]);
    $transaction->typeWithdrawal();
    $transaction->statusSuccess();

    $payment->transaction()->save($transaction);

    $payment->user->balance(true);
});

$factory->afterCreatingState(App\UserOutgoingPayment::class, 'wait', function ($payment, $faker) {
    $payment->payment_aggregator_transaction_id = null;
    $payment->save();

    $transaction = factory(UserPaymentTransaction::class)
        ->make([
            'user_id' => $payment->user_id,
            'sum' => -rand(50, 100),
        ]);
    $transaction->typeWithdrawal();
    $transaction->statusWait();

    $payment->transaction()->save($transaction);

    $payment->user->balance(true);
});

$factory->afterCreatingState(App\UserOutgoingPayment::class, 'processing', function ($payment, $faker) {
    $payment->payment_aggregator_transaction_id = $faker->randomNumber(5);
    $payment->save();

    $transaction = factory(UserPaymentTransaction::class)
        ->make([
            'user_id' => $payment->user_id,
            'sum' => -rand(50, 100)
        ]);
    $transaction->typeWithdrawal();
    $transaction->statusProcessing();

    $payment->transaction()->save($transaction);

    $payment->user->balance(true);
});

$factory->afterCreatingState(App\UserOutgoingPayment::class, 'error', function ($payment, $faker) {

    $payment->payment_aggregator_transaction_id = $faker->randomNumber(5);
    $payment->params = [
        'error' => [
            'message' => 'По вашему запросу ничего не нашлось.',
            'code' => '100',
        ]
    ];
    $payment->save();

    $transaction = factory(UserPaymentTransaction::class)
        ->make([
            'user_id' => $payment->user_id,
            'sum' => -rand(50, 100)
        ]);
    $transaction->typeWithdrawal();
    $transaction->statusError();

    $payment->transaction()->save($transaction);

    $payment->user->balance(true);
});

$factory->afterCreatingState(App\UserOutgoingPayment::class, 'canceled', function ($payment, $faker) {

    $transaction = factory(UserPaymentTransaction::class)
        ->make([
            'user_id' => $payment->user_id,
            'sum' => -rand(50, 100)
        ]);
    $transaction->typeWithdrawal();
    $transaction->statusCanceled();

    $payment->transaction()->save($transaction);

    $payment->user->balance(true);
});
