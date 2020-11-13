<?php

namespace Database\Factories;

use App\User;
use App\UserMoneyTransfer;

class UserMoneyTransferFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = UserMoneyTransfer::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'sender_user_id' => User::factory(),
            'recepient_user_id' => User::factory()
        ];
    }
}
