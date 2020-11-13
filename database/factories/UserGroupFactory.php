<?php

namespace Database\Factories;

use App\UserGroup;

class UserGroupFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = UserGroup::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->realText(30),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    public function administrator()
    {
        return $this->afterMaking(function ($item) {
            foreach ($item->permissions as $name => $value) {
                $item->{$name} = true;
            }
            $item->save();
        })->afterCreating(function ($item) {

        });
    }

    public function user()
    {
        return $this->afterMaking(function ($item) {
            $item->send_message = true;
            $item->blog = true;
            $item->add_forum_post = true;
            $item->shop_enable = true;
            $item->manage_collections = true;
        })->afterCreating(function ($item) {

        });
    }

    public function notify_assignment()
    {
        return $this->afterMaking(function ($item) {
            $item->notify_assignment = true;
        })->afterCreating(function ($item) {

        });
    }

    public function notify_assignment_disable()
    {
        return $this->afterMaking(function ($item) {
            $item->notify_assignment = false;
        })->afterCreating(function ($item) {

        });
    }
}
