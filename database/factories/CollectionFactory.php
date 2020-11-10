<?php

/** @var Factory $factory */

use App\Collection;
use App\Comment;
use App\Enums\StatusEnum;
use App\Enums\UserAccountPermissionValues;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

$factory->define(Collection::class, function (Faker $faker) {

    return [
        'title' => $faker->realText(100),
        'description' => $faker->realText(100),
        'status' => StatusEnum::Accepted,
        'who_can_add' => UserAccountPermissionValues::getRandomKey(),
        'who_can_comment' => UserAccountPermissionValues::getRandomKey(),
        'url' => $faker->url,
        'url_title' => $faker->realText(50),
        'create_user_id' => function () {
            return factory(App\User::class)->states('with_user_permissions')->create()->id;
        },
    ];
});

$factory->afterMakingState(App\Collection::class, 'private', function (Collection $collection, $faker) {
    $collection->statusPrivate();
});

$factory->afterMakingState(App\Collection::class, 'accepted', function (Collection $collection, $faker) {
    $collection->statusAccepted();
});

$factory->afterCreating(App\Collection::class, function (Collection $collection, $faker) {
    $collection->refreshUsersCount();
    $collection->save();
});

$factory->afterCreatingState(App\Collection::class, 'with_comment', function (Collection $collection, $faker) {

    $comment = \factory(Comment::class)
        ->make();

    $collection->comments()
        ->save($comment);
});
