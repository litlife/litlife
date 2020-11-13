<?php

namespace Database\Factories;

use App\Book;
use App\BookViewIp;

class BookViewIpFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = BookViewIp::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'book_id' => Book::factory(),
            'ip' => $this->faker->ipv4,
            'count' => '0'
        ];
    }
}
