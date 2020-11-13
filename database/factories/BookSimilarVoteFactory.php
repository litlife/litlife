<?php

namespace Database\Factories;

use App\Book;
use App\BookSimilarVote;
use App\User;

class BookSimilarVoteFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = BookSimilarVote::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'book_id' => Book::factory(),
            'other_book_id' => Book::factory(),
            'create_user_id' => User::factory(),
            'vote' => 1
        ];
    }
}
