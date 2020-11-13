<?php

namespace Database\Factories;

use App\Blog;
use App\User;
use Database\Factories\Traits\CheckedItems;
use Illuminate\Support\Str;

class BlogFactory extends Factory
{
    use CheckedItems;

    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Blog::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $text = Str::random(10).' '.$this->faker->realText(200);

        return [
            'blog_user_id' => User::factory()->with_user_permissions(),
            'create_user_id' => User::factory()->with_user_permissions(),
            'text' => $text,
            'bb_text' => $text,
            'tree' => null,
            'display_on_home_page' => true
        ];
    }

    public function fixed()
    {
        return $this->afterMaking(function (Blog $blog) {
            $blog->blog_user_id = $blog->create_user_id;
        })->afterCreating(function (Blog $blog) {
            $blog->fix();
        });
    }

    public function sent_for_review()
    {
        return $this->afterMaking(function ($item) {

        })->afterCreating(function ($item) {
            $item->statusSentForReview();
            $item->save();
        });
    }
}
