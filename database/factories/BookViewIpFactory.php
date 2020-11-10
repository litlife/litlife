<?php

use Faker\Generator as Faker;

$factory->define(App\BookViewIp::class, function (Faker $faker) {
    return [
        'book_id' => function () {
            return factory(App\Book::class)->create()->id;
        },
        'ip' => $faker->ipv4,
        'count' => '0'
    ];
});


