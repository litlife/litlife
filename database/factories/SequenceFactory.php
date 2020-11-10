<?php

use App\Book;
use App\Enums\StatusEnum;
use App\Sequence;
use Faker\Generator as Faker;

$factory->define(App\Sequence::class, function (Faker $faker) {
    return [
        'name' => $faker->realText(30),
        'description' => $faker->realText(50),
        'create_user_id' => function () {
            return factory(App\User::class)->create()->id;
        },
        'status' => StatusEnum::Accepted,
        'status_changed_at' => now(),
        'status_changed_user_id' => rand(50000, 100000)
    ];
});

$factory->afterCreatingState(App\Sequence::class, 'with_book', function (Sequence $sequence, $faker) {

    $book = factory(Book::class)
        ->state('with_section')
        ->create();

    $sequence->books()->detach();
    $sequence->books()->attach([$book->id]);
    $sequence->refreshBooksCount();
    $sequence->save();
});

$factory->afterCreatingState(App\Sequence::class, 'with_two_books', function (Sequence $sequence, $faker) {

    $book = factory(Book::class)
        ->state('with_section')
        ->create();

    $book2 = factory(Book::class)
        ->state('with_section')
        ->create();

    $sequence->books()->detach();
    $sequence->books()->attach([$book->id]);
    $sequence->books()->attach([$book2->id]);
    $sequence->refreshBooksCount();
    $sequence->save();
});

$factory->afterMakingState(App\Sequence::class, 'accepted', function (Sequence $sequence, $faker) {
    $sequence->statusAccepted();
});

$factory->afterMakingState(App\Sequence::class, 'sent_for_review', function (Sequence $sequence, $faker) {
    $sequence->statusSentForReview();
});

$factory->afterMakingState(App\Sequence::class, 'private', function (Sequence $sequence, $faker) {
    $sequence->statusPrivate();
});