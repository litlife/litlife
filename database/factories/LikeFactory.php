<?php

use Faker\Generator as Faker;

$factory->define(App\Like::class, function (Faker $faker) {

    return [
        'likeable_type' => 'blog',
        'likeable_id' => function () {
            return factory(App\Blog::class)->create()->id;
        },
        'create_user_id' => function () {
            return factory(App\User::class)->create()->id;
        },
        'ip' => $faker->ipv4
    ];
});

$factory->state(App\Like::class, 'blog', function ($faker) {

    $blog = factory(App\Blog::class)->create();

    return [
        'likeable_type' => 'blog',
        'likeable_id' => $blog->id,
    ];
});
