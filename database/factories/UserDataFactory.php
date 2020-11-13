<?php

namespace Database\Factories;

use App\User;
use App\UserData;

class UserDataFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = UserData::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'favorite_authors' => $this->faker->text(100),
            'favorite_genres' => $this->faker->text(100),
            'favorite_music' => $this->faker->text(100),
            'about_self' => $this->faker->text(100),
            'favorite_quote' => $this->faker->text(100)
        ];
    }
}
