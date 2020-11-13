<?php

namespace Database\Factories;

use App\AdminNote;
use App\Book;
use App\User;

class AdminNoteFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = AdminNote::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'admin_noteable_id' => Book::factory(),
            'admin_noteable_type' => 'book',
            'text' => $this->faker->realText(100),
            'create_user_id' => User::factory()->with_user_permissions()
        ];
    }
}
