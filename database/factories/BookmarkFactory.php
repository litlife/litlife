<?php

use Faker\Generator as Faker;

$factory->define(App\Bookmark::class, function (Faker $faker) {
    return [
        'create_user_id' => function () {
            return factory(App\User::class)->create()->id;
        },
        'title' => $faker->realText(30),
        'url' => \Litlife\Url\Url::fromString($faker->url)->getPath(),
        'created_at' => now(),
        'updated_at' => now(),
    ];
});
