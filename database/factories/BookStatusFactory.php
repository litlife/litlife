<?php

namespace Database\Factories;

use App\Book;
use App\BookStatus;
use App\User;

class BookStatusFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = BookStatus::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'book_id' => Book::factory(),
            'status' => 'readed',
            'user_updated_at' => now()->subMinutes(1)
        ];
    }

    public function readed()
    {
        return $this->afterMaking(function ($item) {
            $item->status = 'readed';
        })->afterCreating(function ($item) {

        });
    }
}
