<?php

namespace Database\Factories;

use App\Book;
use App\PriceChangeLog;

class PriceChangeLogFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = PriceChangeLog::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'book_id' => Book::factory(),
            'price' => rand(10, 100).'.'.rand(10, 99)
        ];
    }
}
