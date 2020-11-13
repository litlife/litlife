<?php

namespace Database\Factories;

use App\GenreGroup;

class GenreGroupFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = GenreGroup::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => uniqid(),
            'book_count' => 0,
            'created_at' => now(),
            'updated_at' => now()
        ];
    }
}
