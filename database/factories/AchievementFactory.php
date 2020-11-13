<?php

namespace Database\Factories;

use App\Achievement;
use App\Image;
use App\User;

class AchievementFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Achievement::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'title' => $this->faker->realText(50),
            'description' => $this->faker->realText(100),
            'image_id' => Image::factory(),
            'create_user_id' => User::factory()
        ];
    }
}
