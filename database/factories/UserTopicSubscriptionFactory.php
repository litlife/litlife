<?php

namespace Database\Factories;

use App\Topic;
use App\User;
use App\UserTopicSubscription;

class UserTopicSubscriptionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = UserTopicSubscription::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'topic_id' => Topic::factory(),
        ];
    }
}
