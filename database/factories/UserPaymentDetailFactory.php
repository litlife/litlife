<?php

/* @var $factory Factory */

use App\User;
use App\UserPaymentDetail;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

$factory->define(UserPaymentDetail::class, function (Faker $faker) {
	return [
		'user_id' => function () {
			return factory(User::class)->create()->id;
		},
		'type' => 'card',
		'number' => '4024007161972749'
	];
});

$factory->afterMakingState(App\UserPaymentDetail::class, 'card', function (UserPaymentDetail $detail, $faker) {
	$detail->type = 'card';
});

$factory->afterMakingState(App\UserPaymentDetail::class, 'ru_card', function (UserPaymentDetail $detail, $faker) {
	$detail->params = ["countryCode" => "RU"];
});

$factory->afterMakingState(App\UserPaymentDetail::class, 'mastercard', function (UserPaymentDetail $detail, $faker) {
	$detail->params = ["brand" => "MASTERCARD"];
});

$factory->afterMakingState(App\UserPaymentDetail::class, 'visa', function (UserPaymentDetail $detail, $faker) {
	$detail->params = ["brand" => "VISA"];
});

$factory->afterMakingState(App\UserPaymentDetail::class, 'webmoney', function (UserPaymentDetail $detail, $faker) {
	$detail->type = 'webmoney';
});

$factory->afterMakingState(App\UserPaymentDetail::class, 'qiwi', function (UserPaymentDetail $detail, $faker) {
	$detail->type = 'qiwi';
});

$factory->afterMakingState(App\UserPaymentDetail::class, 'yandex', function (UserPaymentDetail $detail, $faker) {
	$detail->type = 'yandex';
});
