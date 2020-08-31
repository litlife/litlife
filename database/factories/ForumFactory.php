<?php

use Faker\Generator as Faker;
use Illuminate\Support\Str;

$factory->define(App\Forum::class, function (Faker $faker) {

	return [
		'name' => $faker->realText(70) . ' ' . Str::random(20),
		'description' => $faker->realText(200),
		'create_user_id' => function () {
			return factory(App\User::class)->create()->id;
		},
		'min_message_count' => rand(0, 20),
		'private' => false
	];
});
