<?php

use Faker\Generator as Faker;

$factory->define(App\Conversation::class, function (Faker $faker) {
	//$text = $faker->text(rand(100, 600));
	return [
		'latest_message_id' => 0
	];
});

$factory->afterCreating(App\Conversation::class, function ($conversation, $faker) {

	/*

	*/
});
