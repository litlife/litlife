<?php

use App\Enums\StatusEnum;
use Faker\Generator as Faker;

$factory->define(App\BookKeyword::class, function (Faker $faker) {

	return [
		'book_id' => function () {
			return factory(App\Book::class)->create()->id;
		},
		'keyword_id' => function () {
			return factory(App\Keyword::class)->create()->id;
		},
		'create_user_id' => function () {
			return factory(App\User::class)->create()->id;
		},
		'rating' => '0',
		'created_at' => now(),
		'updated_at' => now(),
		'status' => StatusEnum::Accepted
	];
});

$factory->afterCreatingState(App\BookKeyword::class, 'private', function ($book_keyword, $faker) {
	$book_keyword->keyword->statusPrivate();
	$book_keyword->statusPrivate();
	$book_keyword->push();
});

$factory->afterCreatingState(App\BookKeyword::class, 'on_review', function ($book_keyword, $faker) {
	$book_keyword->keyword->statusSentForReview();
	$book_keyword->statusSentForReview();
	$book_keyword->push();
});

