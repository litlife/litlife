<?php

use App\Enums\StatusEnum;
use App\Manager;
use Faker\Generator as Faker;

$factory->define(App\Manager::class, function (Faker $faker) {

    return [
        'create_user_id' => function () {
            return factory(App\User::class)->states('with_user_group')->create()->id;
        },
        'user_id' => function () {
            return factory(App\User::class)->states('with_user_group')->create()->id;
        },
        'character' => 'editor',
        'comment' => $this->faker->realText(50),
        'status' => StatusEnum::Accepted,
        'can_sale' => false,
        'manageable_type' => 'author',
        'manageable_id' => function () {
            return factory(App\Author::class)
                ->create()
                ->id;
        }
    ];
});

$factory->afterMakingState(App\Manager::class, 'author', function (Manager $request, $faker) {
    $request->character = 'author';
});

$factory->afterMakingState(App\Manager::class, 'character_author', function (Manager $request, $faker) {
    $request->character = 'author';
});

$factory->afterMakingState(App\Manager::class, 'character_editor', function (Manager $request, $faker) {
    $request->character = 'editor';
});

$factory->afterMakingState(App\Manager::class, 'accepted', function (Manager $request, $faker) {
    $request->statusAccepted();
});

$factory->afterMakingState(App\Manager::class, 'on_review', function (Manager $request, $faker) {
    $request->statusSentForReview();
});

$factory->afterMakingState(App\Manager::class, 'starts_review', function (Manager $request, $faker) {
    $request->statusReviewStarts();
});

$factory->afterMakingState(App\Manager::class, 'rejected', function (Manager $request, $faker) {
    $request->statusReject();
});

$factory->afterMakingState(App\Manager::class, 'private', function (Manager $request, $faker) {
    $request->statusPrivate();
});

$factory->afterMakingState(App\Manager::class, 'can_sale', function (Manager $request, $faker) {
    $request->can_sale = true;
});
