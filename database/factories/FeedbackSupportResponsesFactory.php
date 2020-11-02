<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\FeedbackSupportResponses;
use Faker\Generator as Faker;

$factory->define(FeedbackSupportResponses::class, function (Faker $faker) {
	return [
		'text' => $faker->realText(100),
		'face_reaction' => \App\Enums\FaceReactionEnum::getRandomValue()
	];
});
