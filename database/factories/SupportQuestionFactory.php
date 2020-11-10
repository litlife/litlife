<?php

/** @var Factory $factory */

use App\Enums\SupportQuestionTypeEnum;
use App\SupportQuestion;
use App\SupportQuestionMessage;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

$factory->define(SupportQuestion::class, function (Faker $faker) {
    return [
        'category' => SupportQuestionTypeEnum::getRandomValue(),
        'title' => $this->faker->realText(100),
        'create_user_id' => function () {
            return factory(App\User::class)->states('with_user_group')->create()->id;
        }
    ];
});

$factory->afterMakingState(App\SupportQuestion::class, 'accepted', function (SupportQuestion $supportQuestion, $faker) {
    $supportQuestion->statusAccepted();
});

$factory->afterMakingState(App\SupportQuestion::class, 'sent_for_review', function (SupportQuestion $supportQuestion, $faker) {
    $supportQuestion->statusSentForReview();
});

$factory->afterCreatingState(App\SupportQuestion::class, 'with_message', function (SupportQuestion $supportQuestion, $faker) {
    $message = factory(SupportQuestionMessage::class)
        ->make(['create_user_id' => $supportQuestion->create_user_id]);

    $supportQuestion->messages()->save($message);
});

$factory->afterMakingState(App\SupportQuestion::class, 'review_starts', function (SupportQuestion $supportQuestion, $faker) {
    $supportQuestion->statusReviewStarts();
});

$factory->afterCreatingState(App\SupportQuestion::class, 'review_starts', function (SupportQuestion $supportQuestion, $faker) {

    $supportQuestion->status_changed_user_id = factory(App\User::class)
        ->states('with_user_group', 'admin')
        ->create()
        ->id;

    $supportQuestion->save();

});