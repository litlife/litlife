<?php

namespace Database\Factories;

use App\Message;
use App\User;

class MessageFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Message::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $text = $this->faker->realText(100);

        $create_user_id = User::factory()->create()->id;
        //$conversation_id = App\Conversation::factory()->create()->id;

        return [
            'create_user_id' => $create_user_id,
            'text' => $text,
            'is_read' => true,
            'bb_text' => $text,
        ];
    }

    public function configure()
    {
        return $this->afterMaking(function ($item) {
            //
        })->afterCreating(function ($item) {
            $participation = $item->getFirstRecepientParticipation();
            $participation->updateNewMessagesCount();
            $participation->updateLatestMessage();
            $participation->save();

            $participation = $item->getSenderParticipation();
            $participation->updateNewMessagesCount();
            $participation->updateLatestMessage();
            $participation->save();
        });
    }

    public function viewed()
    {
        return $this->afterMaking(function ($item) {
            //
        })->afterCreating(function ($item) {
            $conversation = $item->conversation;

            foreach ($conversation->participations()->get() as $participation) {

                $participation->new_messages_count = 0;
                $participation->latest_seen_message_id = $item->id;
                $participation->latest_message_id = $item->id;
                $participation->save();
            }

            $item->refresh();
        });
    }

    public function not_viewed()
    {
        return $this->afterMaking(function ($item) {
            //
        })->afterCreating(function ($item) {

        });
    }
}
