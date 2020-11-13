<?php

namespace Database\Factories;

use App\Enums\FaceReactionEnum;
use App\FeedbackSupportResponses;

class FeedbackSupportResponsesFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = FeedbackSupportResponses::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'text' => $this->faker->realText(100),
            'face_reaction' => FaceReactionEnum::getRandomValue()
        ];
    }
}
