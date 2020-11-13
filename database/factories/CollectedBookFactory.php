<?php

namespace Database\Factories;

use App\Book;
use App\CollectedBook;
use App\Collection;
use App\User;

class CollectedBookFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = CollectedBook::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'collection_id' => Collection::factory(),
            'book_id' => Book::factory(),
            'create_user_id' => User::factory(),
            'number' => $this->faker->numberBetween(1, 100),
            'comment' => $this->faker->realText(200)
        ];
    }
}
