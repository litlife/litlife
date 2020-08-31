<?php

/* @var $factory Factory */

use App\Topic;
use App\User;
use App\UserTopicSubscription;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

$factory->define(UserTopicSubscription::class, function (Faker $faker) {
	return [
		'user_id' => function () {
			return factory(User::class)->create();
		},
		'topic_id' => function () {
			return factory(Topic::class)->create();
		},
	];
});
