<?php

namespace Database\Factories;

use App\UserEmail;
use App\UserEmailToken;

class UserEmailTokenFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = UserEmailToken::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_email_id' => UserEmail::factory(),
            'token' => uniqid()
        ];
    }
}
