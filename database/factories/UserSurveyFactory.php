<?php

/** @var Factory $factory */

use App\User;
use App\UserSurvey;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

$factory->define(UserSurvey::class, function (Faker $faker) {
    return [
        'create_user_id' => function () {
            return factory(User::class)->create();
        },
    ];
});
