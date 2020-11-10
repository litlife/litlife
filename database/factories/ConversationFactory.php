<?php

use App\Message;
use App\Participation;
use Faker\Generator as Faker;

$factory->define(App\Conversation::class, function (Faker $faker) {
    //$text = $faker->text(rand(100, 600));
    return [
        'latest_message_id' => 0
    ];
});

$factory->afterCreatingState(App\Conversation::class, 'with_not_viewed_message', function ($conversation, $faker) {

    $participation1 = factory(Participation::class)
        ->create(['conversation_id' => $conversation->id]);

    $participation2 = factory(Participation::class)
        ->create(['conversation_id' => $conversation->id]);

    $receptient = $participation1->user;
    $sender = $participation2->user;

    $message = factory(Message::class)
        ->create([
            'conversation_id' => $conversation->id,
            'create_user_id' => $sender->id
        ]);
});

$factory->afterCreatingState(App\Conversation::class, 'with_two_not_viewed_message', function ($conversation, $faker) {

    $recepientParticipation = factory(Participation::class)
        ->create(['conversation_id' => $conversation->id]);

    $senderParticipation = factory(Participation::class)
        ->create(['conversation_id' => $conversation->id]);

    $receptient = $recepientParticipation->user;
    $sender = $senderParticipation->user;

    $message = factory(Message::class)
        ->create([
            'conversation_id' => $conversation->id,
            'create_user_id' => $sender->id
        ]);

    $message2 = factory(Message::class)
        ->create([
            'conversation_id' => $conversation->id,
            'create_user_id' => $sender->id
        ]);

    $recepientParticipation->new_messages_count = 2;
    $recepientParticipation->latest_message_id = $message2->id;
    $recepientParticipation->latest_seen_message_id = null;
    $recepientParticipation->save();

    $senderParticipation->new_messages_count = 0;
    $senderParticipation->latest_message_id = $message2->id;
    $senderParticipation->latest_seen_message_id = $message2->id;
    $senderParticipation->save();

});

$factory->afterCreatingState(App\Conversation::class, 'with_viewed_message', function ($conversation, $faker) {

    $participation1 = factory(Participation::class)
        ->create(['conversation_id' => $conversation->id]);

    $participation2 = factory(Participation::class)
        ->create(['conversation_id' => $conversation->id]);

    $receptient = $participation1->user;
    $sender = $participation2->user;

    $message = factory(Message::class)
        ->create([
            'conversation_id' => $conversation->id,
            'create_user_id' => $sender->id
        ]);

    $participation1->new_messages_count = 0;
    $participation1->latest_message_id = $message->id;
    $participation1->latest_seen_message_id = $message->id;
    $participation1->save();

    $participation2->new_messages_count = 0;
    $participation2->latest_message_id = $message->id;
    $participation2->latest_seen_message_id = $message->id;
    $participation2->save();
});

$factory->afterCreatingState(App\Conversation::class, 'with_two_viewed_message', function ($conversation, $faker) {

    $recepientParticipation = factory(Participation::class)
        ->create(['conversation_id' => $conversation->id]);

    $senderParticipation = factory(Participation::class)
        ->create(['conversation_id' => $conversation->id]);

    $receptient = $recepientParticipation->user;
    $sender = $senderParticipation->user;

    $message = factory(Message::class)
        ->create([
            'conversation_id' => $conversation->id,
            'create_user_id' => $sender->id
        ]);

    $message2 = factory(Message::class)
        ->create([
            'conversation_id' => $conversation->id,
            'create_user_id' => $sender->id
        ]);

    $recepientParticipation->new_messages_count = 0;
    $recepientParticipation->latest_message_id = $message2->id;
    $recepientParticipation->latest_seen_message_id = $message2->id;
    $recepientParticipation->save();

    $senderParticipation->new_messages_count = 0;
    $senderParticipation->latest_message_id = $message2->id;
    $senderParticipation->latest_seen_message_id = $message2->id;
    $senderParticipation->save();

});

$factory->afterCreatingState(App\Conversation::class, 'with_viewed_and_not_viewed_message', function ($conversation, $faker) {

    $recepientParticipation = factory(Participation::class)
        ->create(['conversation_id' => $conversation->id]);

    $senderParticipation = factory(Participation::class)
        ->create(['conversation_id' => $conversation->id]);

    $receptient = $recepientParticipation->user;
    $sender = $senderParticipation->user;

    $message = factory(Message::class)
        ->create([
            'conversation_id' => $conversation->id,
            'create_user_id' => $sender->id
        ]);

    $message2 = factory(Message::class)
        ->create([
            'conversation_id' => $conversation->id,
            'create_user_id' => $sender->id
        ]);

    $recepientParticipation->new_messages_count = 1;
    $recepientParticipation->latest_message_id = $message2->id;
    $recepientParticipation->latest_seen_message_id = $message->id;
    $recepientParticipation->save();

    $senderParticipation->new_messages_count = 0;
    $senderParticipation->latest_message_id = $message2->id;
    $senderParticipation->latest_seen_message_id = $message2->id;
    $senderParticipation->save();

});

$factory->afterCreatingState(App\Conversation::class, 'with_two_users', function ($conversation, $faker) {

    $participation1 = factory(Participation::class)
        ->create(['conversation_id' => $conversation->id]);

    $participation2 = factory(Participation::class)
        ->create(['conversation_id' => $conversation->id]);

    $receptient = $participation1->user;
    $sender = $participation2->user;
});
