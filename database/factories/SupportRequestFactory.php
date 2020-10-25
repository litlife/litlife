<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\SupportRequest;
use Faker\Generator as Faker;

$factory->define(SupportRequest::class, function (Faker $faker) {
	return [
		'create_user_id' => function () {
			return factory(App\User::class)->states('with_user_group')->create()->id;
		},
	];
});
