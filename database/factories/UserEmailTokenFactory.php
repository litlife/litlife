<?php

use Faker\Generator as Faker;

$factory->define(App\UserEmailToken::class, function (Faker $faker) {
	return [
		'user_email_id' => function () {
			return factory(App\UserEmail::class)->create()->id;
		},
		'token' => uniqid()
	];
});
