<?php

namespace Database\Factories;

use App\Activity;
use App\Book;
use App\User;

class ActivityFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Activity::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'description' => 'updated',
            'subject_type' => 'book',
            'subject_id' => Book::factory(),
            'causer_type' => 'user',
            'causer_id' => User::factory(),
        ];
    }
}
