<?php

namespace Database\Factories;

use App\User;
use App\UserEmail;
use Carbon\Carbon;

class UserEmailFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = UserEmail::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => User::factory()->without_email(),
            'email' => uniqid().'@'.uniqid().'.com',
            'confirm' => $this->faker->boolean(),
            'show_in_profile' => $this->faker->boolean(),
            'rescue' => $this->faker->boolean(),
            'notice' => $this->faker->boolean()
        ];
    }

    public function confirmed()
    {
        return $this->afterMaking(function ($item) {
            $item->confirm = true;
        })->afterCreating(function ($item) {

        });
    }

    public function not_confirmed()
    {
        return $this->afterMaking(function ($item) {
            $item->confirm = false;
        })->afterCreating(function ($item) {

        });
    }

    public function noticed()
    {
        return $this->afterMaking(function ($item) {
            $item->notice = true;
        })->afterCreating(function ($item) {

        });
    }

    public function not_noticed()
    {
        return $this->afterMaking(function ($item) {
            $item->notice = false;
        })->afterCreating(function ($item) {

        });
    }

    public function rescued()
    {
        return $this->afterMaking(function ($item) {
            $item->rescue = true;
        })->afterCreating(function ($item) {

        });
    }

    public function not_rescued()
    {
        return $this->afterMaking(function ($item) {
            $item->rescue = false;
        })->afterCreating(function ($item) {

        });
    }

    public function created_before_move_to_new_engine()
    {
        return $this->afterMaking(function ($item) {
            $item->created_at = Carbon::parse($item->getMoveToNewEngineDate())->subMonth();
        })->afterCreating(function ($item) {

        });
    }

    public function show_in_profile()
    {
        return $this->afterMaking(function ($item) {
            $item->show_in_profile = true;
            $item->confirm = true;
        })->afterCreating(function ($item) {

        });
    }

    public function dont_show_in_profile()
    {
        return $this->afterMaking(function ($item) {
            $item->show_in_profile = false;
        })->afterCreating(function ($item) {

        });
    }
}
