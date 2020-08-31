<?php

use Faker\Generator as Faker;

$factory->define(App\UserAuthLog::class, function (Faker $faker) {
	return [
		'user_id' => function () {
			return factory(App\User::class)->create()->id;
		},
		'ip' => $faker->ipv4,
		'user_agent_id' => function () {
			return factory(App\UserAgent::class)->create()->id;
		},
	];
});

$factory->afterCreatingState(App\UserAuthLog::class, 'without_user_agent', function ($log, $faker) {

	$log->user_agent_id = null;
	$log->save();
});