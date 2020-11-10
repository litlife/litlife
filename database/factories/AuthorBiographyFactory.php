<?php

use App\Author;
use Faker\Generator as Faker;

$factory->define(App\AuthorBiography::class, function (Faker $faker) {
    return [
        'author_id' => function () {
            return factory(Author::class)->create()->id;
        },
        'text' => $faker->realText(200),
    ];
});
