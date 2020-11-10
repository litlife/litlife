<?php

/** @var Factory $factory */

use App\Enums\FaceReactionEnum;
use App\FeedbackSupportResponses;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

$factory->define(FeedbackSupportResponses::class, function (Faker $faker) {
    return [
        'text' => $faker->realText(100),
        'face_reaction' => FaceReactionEnum::getRandomValue()
    ];
});
