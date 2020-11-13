<?php

namespace Database\Factories;

use App\Author;
use App\AuthorSaleRequest;
use App\Manager;
use App\User;
use Database\Factories\Traits\CheckedItems;

class AuthorSaleRequestFactory extends Factory
{
    use CheckedItems;

    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = AuthorSaleRequest::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'create_user_id' => User::factory(),
            'author_id' => Author::factory(),
            'manager_id' => function (array $requst) {
                return Manager::factory()->character_author()->create([
                    'user_id' => $requst['create_user_id'],
                    'manageable_id' => $requst['author_id'],
                    'manageable_type' => 'author'
                ])->id;
            },
            'text' => $this->faker->realText(200)
        ];
    }
}
