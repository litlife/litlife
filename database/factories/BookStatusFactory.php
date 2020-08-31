<?php

use App\BookStatus;
use Faker\Generator as Faker;

$factory->define(App\BookStatus::class, function (Faker $faker) {
	return [
		'user_id' => function () {
			return factory(App\User::class)->create()->id;
		},
		'book_id' => function () {
			return factory(App\Book::class)->create()->id;
		},
		'status' => 'readed',
		'user_updated_at' => now()->subMinutes(1)
	];
});

$factory->afterMakingState(App\BookStatus::class, 'readed', function (BookStatus $status, $faker) {
	$status->status = 'readed';
});


