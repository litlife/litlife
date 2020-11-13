<?php

namespace Database\Factories;

use App\Blog;
use App\Book;
use App\Comment;
use App\Complain;
use App\Enums\StatusEnum;
use App\Post;
use App\User;
use Database\Factories\Traits\CheckedItems;

class ComplainFactory extends Factory
{
    use CheckedItems;

    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Complain::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $text = $this->faker->realText(150);

        return [
            'complainable_id' => Comment::factory(),
            'complainable_type' => 'comment',
            'create_user_id' => User::factory(),
            'text' => $text,
            'status' => StatusEnum::OnReview
        ];
    }

    public function comment()
    {
        return $this->afterMaking(function (Complain $complain) {
            //
        })->afterCreating(function (Complain $complain) {
            //
        })->state(function (array $attributes) {
            return [
                'complainable_id' => Comment::factory(),
                'complainable_type' => 'comment'
            ];
        });
    }

    public function post()
    {
        return $this->afterMaking(function (Complain $complain) {
            //
        })->afterCreating(function (Complain $complain) {
            //
        })->state(function (array $attributes) {
            return [
                'complainable_id' => Post::factory(),
                'complainable_type' => 'post'
            ];
        });
    }

    public function wall_post()
    {
        return $this->afterMaking(function (Complain $complain) {
            //
        })->afterCreating(function (Complain $complain) {
            //
        })->state(function (array $attributes) {
            return [
                'complainable_id' => Blog::factory(),
                'complainable_type' => 'blog'
            ];
        });
    }

    public function book()
    {
        return $this->afterMaking(function (Complain $complain) {
            //
        })->afterCreating(function (Complain $complain) {
            //
        })->state(function (array $attributes) {
            return [
                'complainable_id' => Book::factory(),
                'complainable_type' => 'book'
            ];
        });
    }
}
