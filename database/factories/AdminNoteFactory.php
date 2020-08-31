<?php

use App\Enums\StatusEnum;
use Faker\Generator as Faker;

$factory->define(App\AdminNote::class, function (Faker $faker) {

	return [
		'admin_noteable_id' => function () {
			return factory(App\Book::class)->create(['status' => StatusEnum::Accepted])->id;
		},
		'admin_noteable_type' => 'book',
		'text' => $faker->realText(100),
		'create_user_id' => function () {
			return factory(App\User::class)->states('with_user_permissions')->create()->id;
		}
	];
});
