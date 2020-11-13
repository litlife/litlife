<?php

namespace Database\Factories;

use App\DatabaseNotification;
use App\User;

class DatabaseNotificationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = DatabaseNotification::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'id' => $this->faker->uuid,
            'type' => 'App\Notifications\NewCommentReplyNotification',
            'notifiable_type' => 'user',
            'notifiable_id' => User::factory(),
            'data' => '{"title":"\u041e\u0431\u0440\u0430\u0431\u043e\u0442\u043a\u0430 \u043a\u043d\u0438\u0433\u0438 \u0412\u043b\u0430\u0434\u044b\u0447\u0438\u0446\u0430 \u043e\u0437\u0435\u0440\u0430 \u0437\u0430\u0432\u0435\u0440\u0448\u0438\u043b\u0430\u0441\u044c","url":"http:\/\/dev.litlife.club\/books\/316683"}',
            'read_at' => null,
            'created_at' => now(),
            'updated_at' => now()
        ];
    }
}
