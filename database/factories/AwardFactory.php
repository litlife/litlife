<?php

use Faker\Generator as Faker;

$factory->define(App\Award::class, function (Faker $faker) {
	return [
		'title' => $faker->realText(50),
		'description' => $faker->realText(100),
		'create_user_id' => function () {
			return factory(App\User::class)->create()->id;
		},
	];
});
