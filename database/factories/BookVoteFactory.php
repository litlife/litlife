<?php

namespace Database\Factories;

use App\Book;
use App\BookVote;
use App\Jobs\Book\UpdateBookRating;
use App\User;

class BookVoteFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = BookVote::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'book_id' => Book::factory(),
            'create_user_id' => User::factory(),
            'vote' => rand(1, 10),
            'user_updated_at' => now()
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (BookVote $vote) {
            UpdateBookRating::dispatch($vote->book);
        });
    }

    public function male_vote()
    {
        return $this->state(function (array $attributes) {
            return [
                'create_user_id' => function () {
                    return User::factory()->male()->create()
                        ->id;
                }
            ];
        });
    }

    public function female_vote()
    {
        return $this->state(function (array $attributes) {
            return [
                'create_user_id' => function () {
                    return User::factory()->female()->create()
                        ->id;
                }
            ];
        });
    }
}
