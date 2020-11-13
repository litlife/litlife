<?php

namespace Database\Factories;

use App\User;
use App\UserAgent;
use App\UserAuthFail;

class UserAuthFailFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = UserAuthFail::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'ip' => $this->faker->ipv4,
            'user_agent_id' => UserAgent::factory(),
        ];
    }

    public function without_user_agent()
    {
        return $this->afterMaking(function ($item) {
            $item->user_agent_id = null;
        })->afterCreating(function ($item) {

        });
    }
}
