<?php

use Faker\Generator as Faker;

$factory->define(App\UserOnModeration::class, function (Faker $faker) {
    return [
        'user_id' => function () {
            return factory(App\User::class)->create()->id;
        },
        'user_adds_id' => function () {
            return factory(App\User::class)->create()->id;
        }
    ];
});
