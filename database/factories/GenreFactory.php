<?php

use Faker\Generator as Faker;

$factory->define(App\Genre::class, function (Faker $faker) {

    return [
        'genre_group_id' => 1,
        'name' => uniqid(),
        'fb_code' => uniqid(),
        'book_count' => 0,
        'age' => rand(0, 18),
        'created_at' => now(),
        'updated_at' => now()
    ];
});

$factory->state(App\Genre::class, 'with_main_genre', function ($faker) {
    return [
        'genre_group_id' => function () {
            return factory(App\Genre::class)->states('main_genre')
                ->create()->id;
        }
    ];
});

$factory->state(App\Genre::class, 'main_genre', [
    'genre_group_id' => null,
]);


$factory->state(App\Genre::class, 'age_0', [
    'age' => 0,
]);
