<?php

use Faker\Generator as Faker;

$factory->define(App\BookSimilarVote::class, function (Faker $faker) {
	return [
		'book_id' => function () {
			return factory(App\Book::class)->create()->id;
		},
		'other_book_id' => function () {
			return factory(App\Book::class)->create()->id;
		},
		'create_user_id' => function () {
			return factory(App\User::class)->create()->id;
		},
		'vote' => 1
	];
});


