<?php

/** @var Factory $factory */

use App\Collection;
use App\Enums\UserSubscriptionsEventNotificationType;
use App\UserSubscriptionsEventNotification;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;
use Illuminate\Database\Eloquent\Relations\Relation;

$factory->define(UserSubscriptionsEventNotification::class, function (Faker $faker) {

    return [
        'notifiable_user_id' => function () {
            return factory(App\User::class)->create()->id;
        }
    ];

});

$factory->afterMakingState(App\UserSubscriptionsEventNotification::class, 'collection', function (UserSubscriptionsEventNotification $subscription, $faker) {

    $collection = factory(Collection::class)
        ->create();

    foreach (Relation::morphMap() as $alias => $model) {
        if ($model == 'App\Collection') {
            break;
        }
    }

    $subscription->eventable_type = $alias;
    $subscription->eventable_id = $collection->id;
});

$factory->afterMakingState(App\UserSubscriptionsEventNotification::class, 'new_comment', function (UserSubscriptionsEventNotification $subscription, $faker) {
    $subscription->event_type = UserSubscriptionsEventNotificationType::NewComment;
});
