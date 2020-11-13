<?php

namespace Database\Factories;

use App\ReferredUser;
use App\User;

class ReferredUserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ReferredUser::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'referred_by_user_id' => User::factory(),
            'referred_user_id' => User::factory(),
            'comission_buy_book' => rand(1, 10),
            'comission_sell_book' => rand(1, 10)
        ];
    }
}
