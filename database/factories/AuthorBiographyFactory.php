<?php

use Faker\Generator as Faker;

$factory->define(App\AuthorBiography::class, function (Faker $faker) {
	return [
		'author_id' => function () {
			return factory(\App\Author::class)->create()->id;
		},
		'text' => $faker->realText(200),
	];
});
