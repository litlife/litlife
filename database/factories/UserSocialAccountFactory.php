<?php

namespace Database\Factories;

use App\User;
use App\UserSocialAccount;

class UserSocialAccountFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = UserSocialAccount::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'provider_user_id' => rand(1, 10000000000),
            'provider' => 'google',
            'access_token' => $this->faker->linuxPlatformToken,
            'parameters' => '{"id": "'.rand(1, 10000000000).'"}'
        ];
    }
}
