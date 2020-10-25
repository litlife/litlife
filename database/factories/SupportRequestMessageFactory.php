<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\SupportRequestMessage;
use Faker\Generator as Faker;

$factory->define(SupportRequestMessage::class, function (Faker $faker) {
	return [
		'support_request_id' => function () {
			return factory(App\SupportRequest::class)->create()->id;
		},
		'create_user_id' => function () {
			return factory(App\User::class)->states('with_user_group')->create()->id;
		},
		'text' => $faker->realText(200)
	];
});
