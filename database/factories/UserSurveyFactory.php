<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\UserSurvey;
use Faker\Generator as Faker;

$factory->define(UserSurvey::class, function (Faker $faker) {
	return [
		'create_user_id' => function () {
			return factory(\App\User::class)->create();
		},
	];
});
