<?php

namespace Database\Factories;

use App\Conversation;
use App\Message;
use App\Participation;

class ConversationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Conversation::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        //$text = $this->faker->text(rand(100, 600));
        return [
            'latest_message_id' => 0
        ];
    }

    public function with_not_viewed_message()
    {
        return $this->afterCreating(function (Conversation $conversation) {

            $participation1 = Participation::factory()
                ->create(['conversation_id' => $conversation->id]);

            $participation2 = Participation::factory()
                ->create(['conversation_id' => $conversation->id]);

            $receptient = $participation1->user;
            $sender = $participation2->user;

            $message = Message::factory()->create([
                'conversation_id' => $conversation->id,
                'create_user_id' => $sender->id
            ]);

        });
    }

    public function with_two_not_viewed_message()
    {
        return $this->afterCreating(function (Conversation $conversation) {

            $recepientParticipation = Participation::factory()
                ->create(['conversation_id' => $conversation->id]);

            $senderParticipation = Participation::factory()
                ->create(['conversation_id' => $conversation->id]);

            $receptient = $recepientParticipation->user;
            $sender = $senderParticipation->user;

            $message = Message::factory()->create([
                'conversation_id' => $conversation->id,
                'create_user_id' => $sender->id
            ]);

            $message2 = Message::factory()->create([
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
    }

    public function with_viewed_message()
    {
        return $this->afterCreating(function (Conversation $conversation) {

            $participation1 = Participation::factory()
                ->create(['conversation_id' => $conversation->id]);

            $participation2 = Participation::factory()
                ->create(['conversation_id' => $conversation->id]);

            $receptient = $participation1->user;
            $sender = $participation2->user;

            $message = Message::factory()->create([
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
    }

    public function with_two_viewed_message()
    {
        return $this->afterCreating(function (Conversation $conversation) {

            $recepientParticipation = Participation::factory()
                ->create(['conversation_id' => $conversation->id]);

            $senderParticipation = Participation::factory()
                ->create(['conversation_id' => $conversation->id]);

            $receptient = $recepientParticipation->user;
            $sender = $senderParticipation->user;

            $message = Message::factory()->create([
                'conversation_id' => $conversation->id,
                'create_user_id' => $sender->id
            ]);

            $message2 = Message::factory()->create([
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
    }

    public function with_viewed_and_not_viewed_message()
    {
        return $this->afterCreating(function (Conversation $conversation) {

            $recepientParticipation = Participation::factory()
                ->create(['conversation_id' => $conversation->id]);

            $senderParticipation = Participation::factory()
                ->create(['conversation_id' => $conversation->id]);

            $receptient = $recepientParticipation->user;
            $sender = $senderParticipation->user;

            $message = Message::factory()->create([
                'conversation_id' => $conversation->id,
                'create_user_id' => $sender->id
            ]);

            $message2 = Message::factory()->create([
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
    }

    public function with_two_users()
    {
        return $this->afterCreating(function (Conversation $conversation) {

            $participation1 = Participation::factory()
                ->create(['conversation_id' => $conversation->id]);

            $participation2 = Participation::factory()
                ->create(['conversation_id' => $conversation->id]);

            $receptient = $participation1->user;
            $sender = $participation2->user;
        });
    }
}
