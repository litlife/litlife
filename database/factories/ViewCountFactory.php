<?php

use App\Book;
use Faker\Generator as Faker;

$factory->define(App\ViewCount::class, function (Faker $faker) {
    return [
        'book_id' => function () {
            return factory(Book::class)->create()->id;
        },
        'day' => '1',
        'month' => '2',
        'week' => '3',
        'year' => '4',
        'all' => '5'
    ];
});


