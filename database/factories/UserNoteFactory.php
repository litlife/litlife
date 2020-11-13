<?php

namespace Database\Factories;

use App\User;
use App\UserNote;

class UserNoteFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = UserNote::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $text = $this->faker->realText(300);

        return [
            'create_user_id' => User::factory(),
            'text' => $text,
            'bb_text' => $text
        ];
    }
}
