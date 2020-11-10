<?php

use Faker\Generator as Faker;

$factory->define(App\AuthorPhoto::class, function (Faker $faker) {
    return [
        'author_id' => function () {
            return factory(App\Author::class)->create()->id;
        },
        'name' => uniqid() . '.jpeg',
        'create_user_id' => function () {
            return factory(App\User::class)->create()->id;
        },
        'size' => rand(1245, 345346),
    ];
});

$factory->afterMaking(App\AuthorPhoto::class, function ($author_photo, $faker) {

    $author_photo->openImage(__DIR__ . '/../../tests/Feature/images/test.jpeg');
    $author_photo->author->photos()->save($author_photo);

});