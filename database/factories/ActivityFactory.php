<?php

use Faker\Generator as Faker;

$factory->define(App\Activity::class, function (Faker $faker) {

	return [
		'description' => 'updated',
		'subject_type' => 'book',
		'subject_id' => function () {
			return factory(App\Book::class)->create()->id;
		},
		'causer_type' => 'user',
		'causer_id' => function () {
			return factory(App\User::class)->create()->id;
		},
	];
});