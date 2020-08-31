<?php

use Faker\Generator as Faker;

$factory->define(App\CommentVote::class, function (Faker $faker) {

	return [
		'comment_id' => function () {
			return factory(App\Comment::class)->create()->id;
		},
		'vote' => rand(-1, 1),
		'create_user_id' => function () {
			return factory(App\User::class)->create()->id;
		},
		'ip' => $faker->ipv4,
	];
});
