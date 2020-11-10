<?php

use App\Collection;
use App\Comment;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Str;

$factory->define(App\Comment::class, function (Faker $faker) {

    $text = $faker->realText(150) . ' ' . Str::random(10);

    return [
        'commentable_id' => function () {
            return factory(App\Book::class)->create()->id;
        },
        'commentable_type' => 'book',
        'create_user_id' => function () {
            return factory(App\User::class)->create()->id;
        },
        'text' => $text,
        'bb_text' => $text,
        'ip' => $faker->ipv4,
    ];
});

$factory->afterMakingState(App\Comment::class, 'collection', function (Comment $comment, $faker) {

    $map = Relation::morphMap();

    $key = array_search('App\Collection', $map);

    $comment->commentable_type = $key;

    $collection = factory(Collection::class)->create();

    $comment->commentable_id = $collection->id;
});

$factory->afterMakingState(App\Comment::class, 'book', function (Comment $comment, $faker) {

    $comment->commentable_type = 'book';

});

$factory->afterCreatingState(App\Comment::class, 'accepted', function (Comment $comment, $faker) {
    $comment->statusAccepted();
});

$factory->afterCreatingState(App\Comment::class, 'sent_for_review', function (Comment $comment, $faker) {
    $comment->statusSentForReview();
    $comment->save();
});

$factory->afterCreatingState(App\Comment::class, 'private', function (Comment $comment, $faker) {
    $comment->statusPrivate();
    $comment->save();
});
