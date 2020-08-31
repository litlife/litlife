<?php

use App\User;
use Faker\Generator as Faker;

$factory->define(App\BookReadRememberPage::class, function (Faker $faker) {
	return [
		'book_id' => function () {
			return factory(\App\Book::class)
				->create();
		},
		'user_id' => function () {
			return factory(User::class)
				->create();
		},
		'page' => rand(1, 10),
		'inner_section_id' => rand(1, 10),
		'characters_count' => rand(100, 100000)
	];
});

