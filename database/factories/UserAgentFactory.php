<?php

use Faker\Generator as Faker;

$factory->define(App\UserAgent::class, function (Faker $faker) {
	return [
		'value' => $faker->userAgent,
	];
});
