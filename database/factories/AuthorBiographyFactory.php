<?php

namespace Database\Factories;

use App\Author;
use App\AuthorBiography;

class AuthorBiographyFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = AuthorBiography::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'author_id' => Author::factory(),
            'text' => $this->faker->realText(200),
        ];
    }
}
