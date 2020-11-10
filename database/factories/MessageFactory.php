<?php

use Faker\Generator as Faker;

$factory->define(App\Message::class, function (Faker $faker) {
    $text = $faker->realText(100);

    $create_user_id = factory(App\User::class)->create()->id;
    //$conversation_id = factory(App\Conversation::class)->create()->id;

    return [
        'create_user_id' => $create_user_id,
        'text' => $text,
        'is_read' => true,
        'bb_text' => $text,
    ];
});

$factory->afterCreating(App\Message::class, function ($message, $faker) {

    $participation = $message->getFirstRecepientParticipation();
    $participation->updateNewMessagesCount();
    $participation->updateLatestMessage();
    $participation->save();

    $participation = $message->getSenderParticipation();
    $participation->updateNewMessagesCount();
    $participation->updateLatestMessage();
    $participation->save();

    /*
        $conversation = $message->conversation;

        foreach ($conversation->messages()->get() as $message) {

            $participation = $conversation
                ->participations()
                ->where('user_id', $message->create_user_id)
                ->first();

            if (empty($participation))
            {
                $participation = factory(App\Participation::class)
                    ->make([
                        'user_id' => $message->create_user_id,
                        'latest_seen_message_id' => $message->id
                    ]);
                $conversation->participations()->save($participation);
            }
        }

        if ($conversation->participations()->count() < 2)
        {
            $participation = factory(App\Participation::class)->make([
                'latest_seen_message_id' => $message->id
            ]);
            $conversation->participations()->save($participation);
        }

        \App\Jobs\UpdateConversationCounters::dispatch($message->conversation);

        foreach ($conversation->participations()->get() as $participation) {

            \App\Jobs\UpdateParticipationCounters::dispatch($participation);
        }
        */
});

$factory->afterCreatingState(App\Message::class, 'new', function ($message, $faker) {

    $conversation = $message->conversation;

    foreach ($conversation->participations()->get() as $participation) {

        if ($participation->user_id != $message->create_user_id) {
            $participation->latest_seen_message_id = 0;
            $participation->updateNewMessagesCount();
            $participation->updateLatestMessage();
            $participation->save();
        }
    }
});

$factory->afterCreatingState(App\Message::class, 'not_viewed', function ($message, $faker) {

});

$factory->afterCreatingState(App\Message::class, 'viewed', function ($message, $faker) {

    $conversation = $message->conversation;

    foreach ($conversation->participations()->get() as $participation) {

        $participation->new_messages_count = 0;
        $participation->latest_seen_message_id = $message->id;
        $participation->latest_message_id = $message->id;
        $participation->save();
    }

    $message->refresh();
});
