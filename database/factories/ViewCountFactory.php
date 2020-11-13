<?php

namespace Database\Factories;

use App\Book;
use App\ViewCount;

class ViewCountFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ViewCount::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'book_id' => Book::factory(),
            'day' => '1',
            'month' => '2',
            'week' => '3',
            'year' => '4',
            'all' => '5'
        ];
    }
}
