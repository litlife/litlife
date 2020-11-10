<?php

/** @var Factory $factory */

use App\UserSearchSetting;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

$factory->define(UserSearchSetting::class, function (Faker $faker) {
    return [
        'user_id' => function () {
            return factory(App\User::class)->create()->id;
        },
        'name' => 'read_access',
        'value' => 'any'
    ];
});
