<?php

use App\Enums\StatusEnum;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

$factory->define(App\Keyword::class, function (Faker $faker) {

    return [
        'text' => Str::random(10) . ' ' . Str::random(10),
        'create_user_id' => function () {
            return factory(App\User::class)->create()->id;
        },
        'created_at' => now(),
        'updated_at' => now(),
        'status' => StatusEnum::Accepted
    ];
});
