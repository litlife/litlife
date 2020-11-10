<?php

use App\Message;
use App\User;
use Faker\Generator as Faker;

$factory->define(App\MessageDelete::class, function (Faker $faker) {

    return [
        'message_id' => function () {
            return factory(Message::class)->create()->id;
        },
        'user_id' => function () {
            return factory(User::class)->create()->id;
        },
        'deleted_at' => now()
    ];
});
