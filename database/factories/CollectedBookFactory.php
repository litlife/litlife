<?php

/** @var Factory $factory */

use App\CollectedBook;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

$factory->define(CollectedBook::class, function (Faker $faker) {
    return [
        'collection_id' => function () {
            return factory(App\Collection::class)->create()->id;
        },
        'book_id' => function () {
            return factory(App\Book::class)->create()->id;
        },
        'create_user_id' => function () {
            return factory(App\User::class)->create()->id;
        },
        'number' => $faker->numberBetween(1, 100),
        'comment' => $faker->realText(200)
    ];
});
