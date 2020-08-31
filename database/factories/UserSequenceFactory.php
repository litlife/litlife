<?php

use App\User;
use Faker\Generator as Faker;

$factory->define(App\UserSequence::class, function (Faker $faker) {
	return [
		'user_id' => function () {
			return factory(User::class)->create()->id;
		},
		'sequence_id' => function () {
			return factory(\App\Sequence::class)->create()->id;
		},
	];
});
