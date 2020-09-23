<?php

use Faker\Generator as Faker;
use Illuminate\Support\Str;

$factory->define(App\Forum::class, function (Faker $faker) {

	return [
		'name' => $faker->realText(70) . ' ' . Str::random(20),
		'description' => $faker->realText(200),
		'create_user_id' => function () {
			return factory(App\User::class)->create()->id;
		},
		'min_message_count' => rand(0, 20),
		'private' => false
	];
});

$factory->afterMakingState(App\Forum::class, 'private', function (\App\Forum $forum, $faker) {
	$forum->private = true;
});

$factory->afterCreatingState(App\Forum::class, 'with_topic', function (\App\Forum $forum, $faker) {

	$topic = factory(\App\Topic::class)
		->make();

	$forum->topics()->save($topic);
});

$factory->afterCreatingState(App\Forum::class, 'with_user_access', function (\App\Forum $forum, $faker) {

	$user = factory(\App\User::class)
		->create();

	$forum->users_with_access()->sync([$user->id]);
	$forum->refresh();
});