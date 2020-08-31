<?php

/** @var Factory $factory */

use App\ReferredUser;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

$factory->define(ReferredUser::class, function (Faker $faker) {
	return [
		'referred_by_user_id' => function () {
			return factory(App\User::class)->create()->id;
		},
		'referred_user_id' => function () {
			return factory(App\User::class)->create()->id;
		},
		'comission_buy_book' => rand(1, 10),
		'comission_sell_book' => rand(1, 10)
	];
});
