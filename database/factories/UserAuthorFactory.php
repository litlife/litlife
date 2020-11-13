<?php

namespace Database\Factories;

use App\Author;
use App\User;
use App\UserAuthor;

class UserAuthorFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = UserAuthor::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'author_id' => Author::factory(),
        ];
    }
}
