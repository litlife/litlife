<?php

namespace Database\Factories;

use App\Genre;

class GenreFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Genre::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'genre_group_id' => 1,
            'name' => uniqid(),
            'fb_code' => uniqid(),
            'book_count' => 0,
            'age' => rand(0, 18),
            'created_at' => now(),
            'updated_at' => now()
        ];
    }

    public function with_main_genre()
    {
        return $this->afterMaking(function (Genre $genre) {
            //
        })->afterCreating(function (Genre $genre) {
            //
        })->state(function (array $attributes) {
            return [
                'genre_group_id' => function () {
                    return Genre::factory()->main_genre()->create()->id;
                }
            ];
        });
    }

    public function main_genre()
    {
        return $this->afterMaking(function (Genre $genre) {
            //
        })->afterCreating(function (Genre $genre) {
            //
        })->state(function (array $attributes) {
            return [
                'genre_group_id' => null,
            ];
        });
    }

    public function age_0()
    {
        return $this->afterMaking(function (Genre $genre) {
            //
        })->afterCreating(function (Genre $genre) {
            //
        })->state(function (array $attributes) {
            return [
                'age' => 0,
            ];
        });
    }
}
