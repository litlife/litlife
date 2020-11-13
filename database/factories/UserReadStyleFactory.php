<?php

namespace Database\Factories;

use App\User;
use App\UserReadStyle;

class UserReadStyleFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = UserReadStyle::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'font' => 'Arial',
            'align' => 'left',
            'size' => '12',
            'background_color' => '#EEEEEE',
            'card_color' => '#FFFFFF',
            'font_color' => '#000000'
        ];
    }
}
