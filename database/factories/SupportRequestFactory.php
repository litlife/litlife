<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\SupportRequest;
use Faker\Generator as Faker;

$factory->define(SupportRequest::class, function (Faker $faker) {
	return [
		'title' => $this->faker->realText(100),
		'create_user_id' => function () {
			return factory(App\User::class)->states('with_user_group')->create()->id;
		}
	];
});

$factory->afterMakingState(App\SupportRequest::class, 'accepted', function (SupportRequest $supportRequest, $faker) {
	$supportRequest->statusAccepted();
});

$factory->afterMakingState(App\SupportRequest::class, 'sent_for_review', function (SupportRequest $supportRequest, $faker) {
	$supportRequest->statusSentForReview();
});

$factory->afterCreatingState(App\SupportRequest::class, 'with_message', function (SupportRequest $supportRequest, $faker) {
	$message = factory(\App\SupportRequestMessage::class)
		->make(['create_user_id' => $supportRequest->create_user_id]);

	$supportRequest->messages()->save($message);
});

$factory->afterMakingState(App\SupportRequest::class, 'review_starts', function (SupportRequest $supportRequest, $faker) {
	$supportRequest->statusReviewStarts();
});

$factory->afterCreatingState(App\SupportRequest::class, 'review_starts', function (SupportRequest $supportRequest, $faker) {

	$supportRequest->status_changed_user_id = factory(App\User::class)
		->states('with_user_group')
		->create()
		->id;

	$supportRequest->save();

});