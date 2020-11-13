<?php

namespace Database\Factories;

use App\User;
use App\UserOnModeration;

class UserOnModerationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = UserOnModeration::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'user_adds_id' => User::factory()
        ];
    }
}
