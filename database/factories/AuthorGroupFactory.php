<?php

namespace Database\Factories;

use App\Author;
use App\AuthorGroup;
use App\User;

class AuthorGroupFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = AuthorGroup::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'last_name' => $this->faker->lastName,
            'first_name' => $this->faker->firstName,
            'create_user_id' => User::factory()
        ];
    }

    public function with_two_authors()
    {
        return $this->afterCreating(function (AuthorGroup $authorGroup) {
            $author = Author::factory()->create();

            $author->attach_to_group($authorGroup);

            $author = Author::factory()->create();

            $author->attach_to_group($authorGroup);
        });
    }
}
