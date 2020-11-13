<?php

namespace Database\Factories;

use App\Bookmark;
use App\User;
use Litlife\Url\Url;

class BookmarkFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Bookmark::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'create_user_id' => User::factory(),
            'title' => $this->faker->realText(30),
            'url' => Url::fromString($this->faker->url)->getPath(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
