<?php

namespace Database\Factories;

use App\Forum;
use App\Topic;
use App\User;
use Illuminate\Support\Str;

class ForumFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Forum::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->realText(70).' '.Str::random(20),
            'description' => $this->faker->realText(200),
            'create_user_id' => User::factory(),
            'min_message_count' => rand(0, 20),
            'private' => false
        ];
    }

    public function private()
    {
        return $this->afterMaking(function (Forum $forum) {
            $forum->private = true;
        })->afterCreating(function (Forum $forum) {
            //
        });
    }

    public function with_topic()
    {
        return $this->afterMaking(function (Forum $forum) {
            //
        })->afterCreating(function (Forum $forum) {
            $topic = Topic::factory()
                ->make();

            $forum->topics()->save($topic);
        });
    }

    public function with_user_access()
    {
        return $this->afterMaking(function (Forum $forum) {
            //
        })->afterCreating(function (Forum $forum) {
            $user = User::factory()->create();

            $forum->users_with_access()->sync([$user->id]);
            $forum->refresh();
        });
    }
}
