<?php

use App\Author;
use App\User;
use Faker\Generator as Faker;

$factory->define(App\UserAuthor::class, function (Faker $faker) {
    return [
        'user_id' => function () {
            return factory(User::class)->create()->id;
        },
        'author_id' => function () {
            return factory(Author::class)->create()->id;
        },
    ];
});
