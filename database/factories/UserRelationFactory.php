<?php

namespace Database\Factories;

use App\Enums\UserRelationType;
use App\User;
use App\UserRelation;

class UserRelationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = UserRelation::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => User::factory()->with_user_permissions(),
            'user_id2' => User::factory()->with_user_permissions(),
            'status' => UserRelationType::Subscriber,
            'created_at' => now(),
            'updated_at' => now(),
            'user_updated_at' => now()
        ];
    }

    public function configure()
    {
        return $this->afterMaking(function ($item) {

        })->afterCreating(function ($item) {
            if ($item->status == UserRelationType::Friend) {
                UserRelation::updateOrCreate(
                    ['user_id' => $item->user_id2, 'user_id2' => $item->user_id],
                    ['status' => UserRelationType::Friend, 'user_updated_at' => now()]
                );

            }
        });
    }
}
