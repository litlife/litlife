<?php

use Faker\Generator as Faker;

$factory->define(App\AchievementUser::class, function (Faker $faker) {

	return [
		'user_id' => function () {
			return factory(App\User::class)->create()->id;
		},
		'achievement_id' => function () {
			return factory(App\Achievement::class)->create()->id;
		},
		'create_user_id' => function () {
			return factory(App\User::class)->create()->id;
		},
	];
});