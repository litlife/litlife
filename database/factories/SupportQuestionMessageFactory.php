<?php

namespace Database\Factories;

use App\SupportQuestion;
use App\SupportQuestionMessage;
use App\User;

class SupportQuestionMessageFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = SupportQuestionMessage::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'support_question_id' => SupportQuestion::factory(),
            'create_user_id' => User::factory()->with_user_group(),
            'bb_text' => $this->faker->realText(200)
        ];
    }
}
