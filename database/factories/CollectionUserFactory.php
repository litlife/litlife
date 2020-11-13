<?php

namespace Database\Factories;

use App\Collection;
use App\CollectionUser;
use App\User;

class CollectionUserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = CollectionUser::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'collection_id' => Collection::factory(),
            'user_id' => User::factory(),
            'create_user_id' => User::factory(),
            'can_user_manage' => false,
            'can_edit' => false,
            'can_add_books' => false,
            'can_remove_books' => false,
            'can_edit_books_description' => false,
            'can_comment' => false
        ];
    }

    public function collection_who_can_add_me()
    {
        return $this->afterMaking(function (CollectionUser $user) {
            //
        })->afterCreating(function (CollectionUser $user) {
            $user->collection->who_can_add = 'me';
            $user->collection->save();
        });
    }

    public function configure()
    {
        return $this->afterMaking(function (CollectionUser $user) {
            //
        })->afterCreating(function (CollectionUser $user) {
            $user->collection->refreshUsersCount();
            $user->collection->save();
        });
    }
}
