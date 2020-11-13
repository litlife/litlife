<?php

namespace Database\Factories;

use App\Collection;
use App\Comment;
use App\Enums\StatusEnum;
use App\Enums\UserAccountPermissionValues;
use App\User;
use Database\Factories\Traits\CheckedItems;

class CollectionFactory extends Factory
{
    use CheckedItems;

    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Collection::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'title' => $this->faker->realText(100),
            'description' => $this->faker->realText(100),
            'status' => StatusEnum::Accepted,
            'who_can_add' => UserAccountPermissionValues::getRandomKey(),
            'who_can_comment' => UserAccountPermissionValues::getRandomKey(),
            'url' => $this->faker->url,
            'url_title' => $this->faker->realText(50),
            'create_user_id' => User::factory()->with_user_permissions(),
        ];
    }

    public function configure()
    {
        return $this->afterMaking(function (Collection $collection) {
            //
        })->afterCreating(function (Collection $collection) {
            $collection->refreshUsersCount();
            $collection->save();
        });
    }

    public function with_comment()
    {
        return $this->afterMaking(function (Collection $collection) {
            //
        })->afterCreating(function (Collection $collection) {
            $comment = Comment::factory()
                ->make();

            $collection->comments()
                ->save($comment);
        });
    }
}
