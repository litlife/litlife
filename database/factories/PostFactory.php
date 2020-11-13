<?php

namespace Database\Factories;

use App\Enums\StatusEnum;
use App\Enums\VariablesEnum;
use App\Forum;
use App\ForumGroup;
use App\Post;
use App\Topic;
use App\User;
use App\Variable;
use Database\Factories\Traits\CheckedItems;

class PostFactory extends Factory
{
    use CheckedItems;

    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Post::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $text = $this->faker->realText(200);

        return [
            'create_user_id' => User::factory(),
            'html_text' => $text,
            'bb_text' => $text,
            'topic_id' => Topic::factory(),
            'status' => StatusEnum::Accepted,
        ];
    }

    public function with_forum_group()
    {
        return $this->afterMaking(function ($item) {
            $group = ForumGroup::factory()->create();

            $topic = $item->topic;
            $forum = $topic->forum;

            $forum->group()->associate($group);
            $forum->save();
        })->afterCreating(function ($item) {

        });
    }

    public function create_user_with_achievement()
    {
        return $this->afterMaking(function ($item) {
            $item->create_user_id = User::factory()->with_achievement()->create()->id;
        })->afterCreating(function ($item) {

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

    public function idea_forum_posts()
    {
        return $this->afterMaking(function ($item) {

        })->afterCreating(function ($item) {
            $item->fix();
        })->state(function (array $attributes) {
            $value = Variable::where('name', VariablesEnum::IdeaForum)->firstOrFail()->value;

            $forum = Forum::findOrFail($value);

            $topic = Topic::factory()
                ->idea_on_review()
                ->create([
                    'forum_id' => $forum->id
                ]);

            return [
                'forum_id' => function () use ($forum) {
                    return $forum->id;
                },
                'topic_id' => function () use ($topic) {
                    return $topic->id;
                },
            ];
        });
    }

    public function fixed()
    {
        return $this->afterMaking(function ($item) {

        })->afterCreating(function ($item) {
            $item->fix();
            $item->refresh();
        });
    }
}
