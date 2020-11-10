<?php

use Faker\Generator as Faker;
use Illuminate\Support\Str;

$factory->define(App\Invitation::class, function (Faker $faker) {

    return [
        'email' => $faker->email,
        'token' => Str::random(32)
    ];
});
