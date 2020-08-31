<?php

use Faker\Generator as Faker;

$factory->define(App\ForumGroup::class, function (Faker $faker) {

	return [
		'name' => $faker->realText(100),
		'create_user_id' => function () {
			return factory(App\User::class)->create()->id;
		}
	];
});
