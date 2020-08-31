<?php

use Faker\Generator as Faker;

$factory->define(App\Mailing::class, function (Faker $faker) {

	return [
		'email' => $faker->email,
		'priority' => rand(0, 10000)
	];
});