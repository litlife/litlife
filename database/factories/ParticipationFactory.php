<?php

use Faker\Generator as Faker;

$factory->define(App\Participation::class, function (Faker $faker) {

    return [
        'user_id' => function () {
            return factory(App\User::class)->create()->id;
        },
        'conversation_id' => function () {
            return factory(App\Conversation::class)->create()->id;
        },
        'latest_seen_message_id' => 0
    ];
});
