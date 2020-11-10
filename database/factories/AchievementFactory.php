<?php

use Faker\Generator as Faker;

$factory->define(App\Achievement::class, function (Faker $faker) {

    return [
        'title' => $faker->realText(50),
        'description' => $faker->realText(100),
        'image_id' => function () {
            return factory(App\Image::class)->create()->id;
        },
        'create_user_id' => function () {
            return factory(App\User::class)->create()->id;
        },
    ];
});