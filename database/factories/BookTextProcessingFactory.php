<?php

/** @var Factory $factory */

use App\BookTextProcessing;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

$factory->define(BookTextProcessing::class, function (Faker $faker) {
	return [
		'book_id' => function () {
			return factory(App\Book::class)
				->create(['forbid_to_change' => true])
				->id;
		},
		'create_user_id' => function () {
			$user = factory(App\User::class)->create();
			$user->group->create_text_processing_books = true;
			$user->push();

			return $user->id;
		},
	];
});