<?php

/** @var Factory $factory */

use App\PriceChangeLog;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

$factory->define(PriceChangeLog::class, function (Faker $faker) {
    return [
        'book_id' => function () {
            return factory(App\Book::class)
                ->create()->id;
        },
        'price' => rand(10, 100) . '.' . rand(10, 99)
    ];
});
