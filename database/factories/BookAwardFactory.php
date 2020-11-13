<?php

namespace Database\Factories;

use App\Award;
use App\Book;
use App\BookAward;
use App\User;

class BookAwardFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = BookAward::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'book_id' => Book::factory(),
            'award_id' => Award::factory(),
            'create_user_id' => User::factory(),
        ];
    }
}
