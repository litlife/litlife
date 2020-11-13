<?php

namespace Database\Factories;

use App\Blog;
use App\Like;
use App\User;

class LikeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Like::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'likeable_type' => 'blog',
            'likeable_id' => Blog::factory(),
            'create_user_id' => User::factory(),
            'ip' => $this->faker->ipv4
        ];
    }

    public function blog()
    {
        return $this->afterMaking(function (Like $like) {
            //
        })->afterCreating(function (Like $like) {
            //
        })->state(function (array $attributes) {
            $blog = Blog::factory()->create();

            return [
                'likeable_type' => 'blog',
                'likeable_id' => $blog->id,
            ];
        });
    }
}
