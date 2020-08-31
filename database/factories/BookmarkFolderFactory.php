<?php

use Faker\Generator as Faker;

$factory->define(App\BookmarkFolder::class, function (Faker $faker) {
	return [
		'create_user_id' => function () {
			return factory(App\User::class)->create()->id;
		},
		'title' => $faker->realText(50),
		'created_at' => now(),
		'updated_at' => now(),
	];
});
