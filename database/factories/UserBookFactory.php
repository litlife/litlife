<?php

use App\User;
use Faker\Generator as Faker;

$factory->define(App\UserBook::class, function (Faker $faker) {
	return [
		'user_id' => function () {
			return factory(User::class)->create()->id;
		},
		'book_id' => function () {
			return factory(\App\Book::class)->create()->id;
		},
	];
});
