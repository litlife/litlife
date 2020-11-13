<?php

namespace Database\Factories;

use App\Award;
use App\User;

class AwardFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Award::class;

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
            'create_user_id' => User::factory(),
        ];
    }
}
