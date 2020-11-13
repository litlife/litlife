<?php

namespace Database\Factories;

use App\User;
use App\UserSurvey;

class UserSurveyFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = UserSurvey::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'create_user_id' => User::factory(),
        ];
    }
}
