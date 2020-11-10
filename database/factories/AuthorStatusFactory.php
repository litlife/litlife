<?php

use App\AuthorStatus;
use Faker\Generator as Faker;

$factory->define(App\AuthorStatus::class, function (Faker $faker) {
    return [
        'user_id' => function () {
            return factory(App\User::class)->create()->id;
        },
        'author_id' => function () {
            return factory(App\Author::class)->create()->id;
        },
        'status' => 'readed',
        'user_updated_at' => now()
    ];
});

$factory->afterMakingState(App\AuthorStatus::class, 'read_later', function (AuthorStatus $author_status, $faker) {
    $author_status->status = 'read_later';
});