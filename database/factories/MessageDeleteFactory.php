<?php

use Faker\Generator as Faker;

$factory->define(App\MessageDelete::class, function (Faker $faker) {

	return [
		'message_id' => function () {
			return factory(\App\Message::class)->create()->id;
		},
		'user_id' => function () {
			return factory(\App\User::class)->create()->id;
		},
		'deleted_at' => now()
	];
});
