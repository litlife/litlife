<?php

namespace Database\Factories;

use App\User;
use App\UserAccountPermission;

class UserAccountPermissionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = UserAccountPermission::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => User::factory()
        ];
    }
}
