<?php

use App\User;
use Faker\Generator as Faker;

$factory->define(App\UserReadStyle::class, function (Faker $faker) {
	return [
		'user_id' => function () {
			return factory(User::class)->create()->id;
		},
		'font' => 'Arial',
		'align' => 'left',
		'size' => '12',
		'background_color' => '#EEEEEE',
		'card_color' => '#FFFFFF',
		'font_color' => '#000000'
	];
});
