<?php

namespace Database\Factories;

use App\Comment;
use App\CommentVote;
use App\User;

class CommentVoteFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = CommentVote::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'comment_id' => Comment::factory(),
            'vote' => rand(-1, 1),
            'create_user_id' => User::factory(),
            'ip' => $this->faker->ipv4,
        ];
    }
}
