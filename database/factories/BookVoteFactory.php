<?php

use App\BookVote;
use App\Jobs\Book\UpdateBookRating;
use Faker\Generator as Faker;

$factory->define(App\BookVote::class, function (Faker $faker) {
	return [
		'book_id' => function () {
			return factory(App\Book::class)->create()->id;
		},
		'create_user_id' => function () {
			return factory(App\User::class)->create()->id;
		},
		'vote' => rand(1, 10),
		'user_updated_at' => now()
	];
});

$factory->afterCreating(App\BookVote::class, function (BookVote $book_vote, $faker) {

	UpdateBookRating::dispatch($book_vote->book);
});

$factory->state(App\BookVote::class, 'male_vote', function ($faker) {

	return [
		'create_user_id' => function () {
			return factory(App\User::class)
				->states('male')
				->create()
				->id;
		}
	];
});

$factory->state(App\BookVote::class, 'female_vote', function ($faker) {

	return [
		'create_user_id' => function () {
			return factory(App\User::class)
				->states('female')
				->create()
				->id;
		}
	];
});


