<?php

use App\Enums\TextBlockShowEnum;
use App\TextBlock;
use Faker\Generator as Faker;

$factory->define(App\TextBlock::class, function (Faker $faker) {

	return [
		'name' => uniqid(),
		'text' => $faker->realText(rand(50, 100)),
		'show_for_all' => TextBlockShowEnum::Administration,
		'user_id' => function () {
			return factory(App\User::class)->states('with_user_group')->create()->id;
		},
		'user_edited_at' => now()
	];
});

$factory->afterMakingState(App\TextBlock::class, 'show_for_admin', function (TextBlock $textBlock, $faker) {
	$textBlock->show_for_all = TextBlockShowEnum::Administration;
});

$factory->afterMakingState(App\TextBlock::class, 'show_for_all', function (TextBlock $textBlock, $faker) {
	$textBlock->show_for_all = TextBlockShowEnum::All;
});
