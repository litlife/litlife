<?php

use App\Complain;
use App\Enums\StatusEnum;
use Faker\Generator as Faker;

$factory->define(App\Complain::class, function (Faker $faker) {

    $text = $faker->realText(150);

    return [
        'complainable_id' => function () {
            return factory(App\Comment::class)->create()->id;
        },
        'complainable_type' => 'comment',
        'create_user_id' => function () {
            return factory(App\User::class)->create()->id;
        },
        'text' => $text,
        'status' => StatusEnum::OnReview
    ];
});

$factory->state(App\Complain::class, 'comment', function ($faker) {
    return [
        'complainable_id' => function () {
            return factory(App\Comment::class)->create()->id;
        },
        'complainable_type' => 'comment'
    ];
});

$factory->state(App\Complain::class, 'post', function ($faker) {
    return [
        'complainable_id' => function () {
            return factory(App\Post::class)->create()->id;
        },
        'complainable_type' => 'post'
    ];
});

$factory->state(App\Complain::class, 'wall_post', function ($faker) {
    return [
        'complainable_id' => function () {
            return factory(App\Blog::class)->create()->id;
        },
        'complainable_type' => 'blog'
    ];
});

$factory->state(App\Complain::class, 'book', function ($faker) {
    return [
        'complainable_id' => function () {
            return factory(App\Book::class)->create()->id;
        },
        'complainable_type' => 'book'
    ];
});

$factory->afterMakingState(App\Complain::class, 'accepted', function (Complain $complain, $faker) {
    $complain->statusAccepted();
});

$factory->afterMakingState(App\Complain::class, 'review_starts', function (Complain $complain, $faker) {
    $complain->statusReviewStarts();
});

$factory->afterMakingState(App\Complain::class, 'sent_for_review', function (Complain $complain, $faker) {
    $complain->statusSentForReview();
});
