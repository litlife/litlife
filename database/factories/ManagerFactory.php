<?php

namespace Database\Factories;

use App\Author;
use App\Enums\StatusEnum;
use App\Manager;
use App\User;
use Database\Factories\Traits\CheckedItems;

class ManagerFactory extends Factory
{
    use CheckedItems;

    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Manager::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'create_user_id' => User::factory()->with_user_group(),
            'user_id' => User::factory()->with_user_group(),
            'character' => 'editor',
            'comment' => $this->faker->realText(50),
            'status' => StatusEnum::Accepted,
            'can_sale' => false,
            'manageable_type' => 'author',
            'manageable_id' => Author::factory()
        ];
    }

    public function character_author()
    {
        return $this->afterMaking(function (Manager $request) {
            $request->character = 'author';
        })->afterCreating(function (Manager $request) {
            //
        });
    }

    public function character_editor()
    {
        return $this->afterMaking(function (Manager $request) {
            $request->character = 'editor';
        })->afterCreating(function (Manager $request) {
            //
        });
    }

    public function can_sale()
    {
        return $this->afterMaking(function (Manager $request) {
            $request->can_sale = true;
        })->afterCreating(function (Manager $request) {
            //
        });
    }
}
