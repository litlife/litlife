<?php

namespace Database\Factories;

use App\Message;
use App\MessageDelete;
use App\User;

class MessageDeleteFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = MessageDelete::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'message_id' => Message::factory(),
            'user_id' => User::factory(),
            'deleted_at' => now()
        ];
    }
}
