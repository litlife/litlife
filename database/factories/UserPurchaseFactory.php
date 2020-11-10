<?php

/* @var $factory Factory */

use App\Book;
use App\User;
use App\UserPaymentTransaction;
use App\UserPurchase;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

$factory->define(UserPurchase::class, function (Faker $faker) {

    $price = rand(100, 150);

    $site_commission = rand(10, 30);

    return [
        'buyer_user_id' => function () {
            return factory(User::class)
                ->create()
                ->id;
        },
        'seller_user_id' => function () {
            return factory(User::class)
                ->create()
                ->id;
        },
        'price' => $price,
        'site_commission' => config('litlife.comission')
    ];
});

$factory->afterMakingState(App\UserPurchase::class, 'book', function ($purchase, $faker) {

    $book = factory(Book::class)
        ->states('with_section', 'with_writer')
        ->create();

    $purchase->purchasable_type = 'book';
    $purchase->purchasable_id = $book->id;
});

$factory->afterCreatingState(App\UserPurchase::class, 'book', function ($purchase, $faker) {

    $comission_sum = (($purchase->price / 100) * $purchase->site_commission);
    $seller_sum = $purchase->price - $comission_sum;

    $transaction = factory(UserPaymentTransaction::class)
        ->make([
            'user_id' => $purchase->buyer_user_id,
            'sum' => -$purchase->price
        ]);
    $transaction->typeBuy();
    $transaction->statusSuccess();
    $purchase->transaction()->save($transaction);

    $transaction = factory(UserPaymentTransaction::class)
        ->make([
            'user_id' => $purchase->seller_user_id,
            'sum' => $seller_sum
        ]);
    $transaction->typeSell();
    $transaction->statusSuccess();
    $purchase->transaction()->save($transaction);

    $transaction = factory(UserPaymentTransaction::class)
        ->make([
            'user_id' => 0,
            'sum' => $comission_sum
        ]);
    $transaction->typeComission();
    $transaction->statusSuccess();
    $purchase->transaction()->save($transaction);
});

$factory->afterMakingState(App\UserPurchase::class, 'with_seller_referer', function ($purchase, $faker) {

    $purchase->seller_user_id = factory(User::class)
        ->state('referred')
        ->create()->id;
});

$factory->afterCreatingState(App\UserPurchase::class, 'with_seller_referer', function ($purchase, $faker) {

    $transaction = factory(UserPaymentTransaction::class)
        ->make([
            'user_id' => $purchase->seller->referred_by_user->first()->id,
            'sum' => 2
        ]);
    $transaction->typeComissionRefererSeller();
    $transaction->statusSuccess();
    $purchase->transaction()->save($transaction);
});

$factory->afterMakingState(App\UserPurchase::class, 'with_buyer_referer', function ($purchase, $faker) {

    $purchase->buyer_user_id = factory(User::class)
        ->state('referred')
        ->create()
        ->id;
});

$factory->afterCreatingState(App\UserPurchase::class, 'with_buyer_referer', function ($purchase, $faker) {

    $transaction = factory(UserPaymentTransaction::class)
        ->make([
            'user_id' => $purchase->buyer->referred_by_user->first()->id,
            'sum' => 3
        ]);

    $transaction->typeComissionRefererBuyer();
    $transaction->statusSuccess();
    $purchase->transaction()->save($transaction);
});


$factory->afterCreatingState(App\UserPurchase::class, 'canceled', function ($purchase, $faker) {

    $purchase->cancel();
    $purchase->save();
});