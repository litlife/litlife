<?php

namespace Database\Factories;

use App\Achievement;
use App\AchievementUser;
use App\User;

class AchievementUserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = AchievementUser::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'achievement_id' => Achievement::factory(),
            'create_user_id' => User::factory()
        ];
    }
}
