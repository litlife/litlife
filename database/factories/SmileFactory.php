<?php

use Faker\Generator as Faker;

$factory->define(App\Smile::class, function (Faker $faker) {

    return [
        'name' => uniqid() . '.jpg',
        'simple_form' => ':' . uniqid() . ':',
        'description' => uniqid(),
        'for' => null
    ];
});

$factory->afterMakingState(App\Smile::class, 'for_new_year', function ($smile, $faker) {
    $smile->for = 'NewYear';
});
