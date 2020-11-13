<?php

namespace Database\Factories;

use App\Mailing;

class MailingFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Mailing::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'email' => $this->faker->email,
            'priority' => rand(0, 10000)
        ];
    }
}
