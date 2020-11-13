<?php

namespace Database\Factories;

use App\ForumGroup;
use App\User;

class ForumGroupFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ForumGroup::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->realText(100),
            'create_user_id' => User::factory()
        ];
    }
}
