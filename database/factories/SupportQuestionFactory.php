<?php

namespace Database\Factories;

use App\Enums\SupportQuestionTypeEnum;
use App\SupportQuestion;
use App\SupportQuestionMessage;
use App\User;
use Database\Factories\Traits\CheckedItems;

class SupportQuestionFactory extends Factory
{
    use CheckedItems;

    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = SupportQuestion::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'category' => SupportQuestionTypeEnum::getRandomValue(),
            'title' => $this->faker->realText(100),
            'create_user_id' => User::factory()->with_user_group()
        ];
    }

    public function with_message()
    {
        return $this->afterMaking(function ($item) {

        })->afterCreating(function ($item) {
            $message = SupportQuestionMessage::factory()
                ->make(['create_user_id' => $item->create_user_id]);

            $item->messages()->save($message);
        });
    }
}
