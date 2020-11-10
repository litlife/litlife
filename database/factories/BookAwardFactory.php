<?php

use Faker\Generator as Faker;

$factory->define(App\BookAward::class, function (Faker $faker) {
    return [
        'book_id' => function () {
            return factory(App\Book::class)->create()->id;
        },
        'award_id' => function () {
            return factory(App\Award::class)->create()->id;
        },
        'create_user_id' => function () {
            return factory(App\User::class)->create()->id;
        },
    ];
});
