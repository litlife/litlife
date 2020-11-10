<?php

/* @var $factory Factory */

use App\UserMoneyTransfer;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

$factory->define(UserMoneyTransfer::class, function (Faker $faker) {
    return [
        'sender_user_id' => function () {
            return factory(App\User::class)->create()->id;
        },
        'recepient_user_id' => function () {
            return factory(App\User::class)->create()->id;
        }
    ];
});
