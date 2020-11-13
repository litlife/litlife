<?php

namespace Database\Factories;

use App\Book;
use App\BookReadRememberPage;
use App\User;

class BookReadRememberPageFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = BookReadRememberPage::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'book_id' => Book::factory(),
            'user_id' => User::factory(),
            'page' => rand(1, 10),
            'inner_section_id' => rand(1, 10),
            'characters_count' => rand(100, 100000)
        ];
    }
}
