<?php

use Faker\Generator as Faker;

$factory->define(App\UserNote::class, function (Faker $faker) {

    $text = $faker->realText(300);

    return [
        'create_user_id' => function () {
            return factory(App\User::class)->create()->id;
        },
        'text' => $text,
        'bb_text' => $text
    ];
});
