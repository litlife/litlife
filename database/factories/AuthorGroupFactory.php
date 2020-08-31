<?php

/* @var $factory Factory */

use App\AuthorGroup;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

$factory->define(AuthorGroup::class, function (Faker $faker) {
	return [
		'last_name' => $faker->lastName,
		'first_name' => $faker->firstName,
		'create_user_id' => function () {
			return factory(App\User::class)->create()->id;
		}
	];
});

$factory->afterCreatingState(App\AuthorGroup::class, 'with_two_authors', function (AuthorGroup $authorGroup, $faker) {

	$author = factory(\App\Author::class)
		->create();

	$author->attach_to_group($authorGroup);

	$author = factory(\App\Author::class)
		->create();

	$author->attach_to_group($authorGroup);
});
