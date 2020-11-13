<?php

namespace Database\Factories;

use App\Sequence;
use App\User;
use App\UserSequence;

class UserSequenceFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = UserSequence::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'sequence_id' => Sequence::factory()
        ];
    }
}
