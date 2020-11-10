<?php

/** @var Factory $factory */

use App\SupportQuestionMessage;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

$factory->define(SupportQuestionMessage::class, function (Faker $faker) {
    return [
        'support_question_id' => function () {
            return factory(App\SupportQuestion::class)->create()->id;
        },
        'create_user_id' => function () {
            return factory(App\User::class)->states('with_user_group')->create()->id;
        },
        'bb_text' => $faker->realText(200)
    ];
});
