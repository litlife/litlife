<?php

namespace Database\Factories;

use App\User;
use App\UserSearchSetting;

class UserSearchSettingFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = UserSearchSetting::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'name' => 'read_access',
            'value' => 'any'
        ];
    }
}
