<?php

namespace Database\Factories;

use App\Author;
use App\AuthorRepeat;
use App\User;

class AuthorRepeatFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = AuthorRepeat::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'comment' => $this->faker->realText(200),
            'create_user_id' => User::factory()
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (AuthorRepeat $author_repeat) {
            $author = Author::factory()->create();
            $author2 = Author::factory()->create();

            $author_repeat->authors()
                ->attach([$author->id, $author2->id]);
        });
    }
}
