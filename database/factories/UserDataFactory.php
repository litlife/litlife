<?php

use Faker\Generator as Faker;

$factory->define(App\UserData::class, function (Faker $faker) {
	return [
		'user_id' => function () {
			return factory(App\User::class)->create()->id;
		},
		'favorite_authors' => $faker->text(100),
		'favorite_genres' => $faker->text(100),
		'favorite_music' => $faker->text(100),
		'about_self' => $faker->text(100),
		'favorite_quote' => $faker->text(100)
	];
});
