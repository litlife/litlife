<?php

use Faker\Generator as Faker;

$factory->define(App\GenreGroup::class, function (Faker $faker) {

    return [
        'name' => uniqid(),
        'book_count' => 0,
        'created_at' => now(),
        'updated_at' => now()
    ];
});
