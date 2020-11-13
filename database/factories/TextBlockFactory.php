<?php

namespace Database\Factories;

use App\Enums\TextBlockShowEnum;
use App\TextBlock;
use App\User;

class TextBlockFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = TextBlock::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => uniqid(),
            'text' => $this->faker->realText(rand(50, 100)),
            'show_for_all' => TextBlockShowEnum::Administration,
            'user_id' => User::factory()->with_user_group(),
            'user_edited_at' => now()
        ];
    }

    public function show_for_admin()
    {
        return $this->afterMaking(function ($item) {
            $item->show_for_all = TextBlockShowEnum::Administration;
        })->afterCreating(function ($item) {

        });
    }

    public function show_for_all()
    {
        return $this->afterMaking(function ($item) {
            $item->show_for_all = TextBlockShowEnum::All;
        })->afterCreating(function ($item) {

        });
    }
}
