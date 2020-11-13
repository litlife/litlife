<?php

namespace Database\Factories;

use App\PasswordReset;
use App\User;

class PasswordResetFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = PasswordReset::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => 0,
            'email' => '',
            'used_at' => null,
            'created_at' => now(),
            'updated_at' => now()
        ];
    }

    public function with_user_with_confirmed_email()
    {
        return $this->afterMaking(function ($item) {

        })->afterCreating(function ($item) {

        })->state(function (array $attributes) {
            $user = User::factory()->with_confirmed_email()->create();

            $email = $user->emails()->first();

            return [
                'user_id' => $user->id,
                'email' => $email->email,
            ];
        });
    }

    public function used()
    {
        return $this->afterMaking(function ($item) {
            $item->used();
        })->afterCreating(function ($item) {

        });
    }
}
