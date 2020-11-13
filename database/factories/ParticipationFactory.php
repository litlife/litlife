<?php

namespace Database\Factories;

use App\Conversation;
use App\Participation;
use App\User;

class ParticipationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Participation::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'conversation_id' => Conversation::factory(),
            'latest_seen_message_id' => 0
        ];
    }
}
