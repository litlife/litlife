<?php

namespace Database\Factories;

use App\Enums\TopicLabelEnum;
use App\Forum;
use App\Post;
use App\Topic;
use App\User;
use Illuminate\Support\Str;

class TopicFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Topic::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->realText(70).' '.Str::random(20),
            'description' => $this->faker->realText(100),
            'create_user_id' => User::factory(),
            'forum_id' => Forum::factory(),
        ];
    }

    public function idea_implemented()
    {
        return $this->afterMaking(function ($item) {
            $item->label = TopicLabelEnum::IdeaImplemented;
        })->afterCreating(function ($item) {

        });
    }

    public function idea_on_review()
    {
        return $this->afterMaking(function ($item) {
            $item->label = TopicLabelEnum::IdeaOnReview;
        })->afterCreating(function ($item) {

        });
    }

    public function idea_rejected()
    {
        return $this->afterMaking(function ($item) {
            $item->label = TopicLabelEnum::IdeaRejected;
        })->afterCreating(function ($item) {

        });
    }

    public function idea_in_progress()
    {
        return $this->afterMaking(function ($item) {
            $item->label = TopicLabelEnum::IdeaInProgress;
        })->afterCreating(function ($item) {

        });
    }

    public function archived()
    {
        return $this->afterMaking(function ($item) {
            $item->archive();
        })->afterCreating(function ($item) {

        });
    }

    public function closed()
    {
        return $this->afterMaking(function ($item) {
            $item->close();
        })->afterCreating(function ($item) {

        });
    }

    public function with_post()
    {
        return $this->afterMaking(function ($item) {

        })->afterCreating(function ($item) {
            $post = Post::factory()->make();
            $item->posts()->save($post);
        });
    }

    public function with_fixed_post()
    {
        return $this->afterMaking(function ($item) {

        })->afterCreating(function ($item) {
            $post = Post::factory()->make();
            $item->posts()->save($post);
            $post->fix();
        });
    }
}
