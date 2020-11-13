<?php

namespace Database\Factories;

use App\Book;
use App\BookTextProcessing;
use App\User;

class BookTextProcessingFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = BookTextProcessing::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'book_id' => Book::factory()->state(['forbid_to_change' => true]),
            'create_user_id' => function () {
                $user = User::factory()->create();
                $user->group->create_text_processing_books = true;
                $user->push();

                return $user->id;
            },
        ];
    }
}
