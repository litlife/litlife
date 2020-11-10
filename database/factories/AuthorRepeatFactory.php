<?php

use App\Author;
use Faker\Generator as Faker;

$factory->define(App\AuthorRepeat::class, function (Faker $faker) {
    return [
        'comment' => $faker->realText(200),
        'create_user_id' => function () {
            return factory(App\User::class)->create()->id;
        }
    ];
});

$factory->afterCreating(App\AuthorRepeat::class, function ($author_repeat, $faker) {

    $author = factory(Author::class)->create();
    $author2 = factory(Author::class)->create();

    $author_repeat->authors()
        ->attach([$author->id, $author2->id]);
});