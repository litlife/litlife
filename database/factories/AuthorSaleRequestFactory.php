<?php

/* @var $factory Factory */

use App\Author;
use App\AuthorSaleRequest;
use App\Manager;
use App\User;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

$factory->define(AuthorSaleRequest::class, function (Faker $faker) {
    return [
        'create_user_id' => function () {
            return factory(User::class)
                ->create()
                ->id;
        },
        'author_id' => function () {
            return factory(Author::class)
                ->create()
                ->id;
        },
        'manager_id' => function (array $requst) {
            return factory(Manager::class)
                ->state('character_author')
                ->create([
                    'user_id' => $requst['create_user_id'],
                    'manageable_id' => $requst['author_id'],
                    'manageable_type' => 'author'
                ])
                ->id;
        },
        'text' => $faker->realText(200)
    ];
});

$factory->afterCreatingState(App\AuthorSaleRequest::class, 'accepted', function ($request, $faker) {
    $request->statusAccepted();
    $request->save();
});

$factory->afterCreatingState(App\AuthorSaleRequest::class, 'on_review', function ($request, $faker) {
    $request->statusSentForReview();
    $request->save();
});

$factory->afterCreatingState(App\AuthorSaleRequest::class, 'starts_review', function ($request, $faker) {
    $request->statusReviewStarts();
    $request->save();
});

$factory->afterCreatingState(App\AuthorSaleRequest::class, 'rejected', function (AuthorSaleRequest $request, $faker) {
    $request->statusReject();
    $request->save();
});