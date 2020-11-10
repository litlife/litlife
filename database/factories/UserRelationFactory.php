<?php

use App\Enums\UserRelationType;
use App\UserRelation;
use Faker\Generator as Faker;

$factory->define(App\UserRelation::class, function (Faker $faker) {
    return [
        'user_id' => function () {
            return factory(App\User::class)->states('with_user_permissions')->create()->id;
        },
        'user_id2' => function () {
            return factory(App\User::class)->states('with_user_permissions')->create()->id;
        },
        'status' => UserRelationType::Subscriber,
        'created_at' => now(),
        'updated_at' => now(),
        'user_updated_at' => now()
    ];
});

$factory->afterCreating(App\UserRelation::class, function ($relation, $faker) {

    if ($relation->status == UserRelationType::Friend) {
        UserRelation::updateOrCreate(
            ['user_id' => $relation->user_id2, 'user_id2' => $relation->user_id],
            ['status' => UserRelationType::Friend, 'user_updated_at' => now()]
        );

    }
});
