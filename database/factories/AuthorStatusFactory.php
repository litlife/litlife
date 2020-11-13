<?php

namespace Database\Factories;

use App\Author;
use App\AuthorStatus;
use App\User;

class AuthorStatusFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = AuthorStatus::class;

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
            'status' => 'readed',
            'user_updated_at' => now()
        ];
    }

    public function read_later()
    {
        return $this->afterMaking(function (AuthorStatus $author_status) {
            $author_status->status = 'read_later';
        });
    }
}
