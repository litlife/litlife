<?php

namespace Database\Factories;

use App\Enums\StatusEnum;
use App\Keyword;
use App\User;
use Database\Factories\Traits\CheckedItems;
use Illuminate\Support\Str;

class KeywordFactory extends Factory
{
    use CheckedItems;

    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Keyword::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'text' => Str::random(10).' '.Str::random(10),
            'create_user_id' => User::factory(),
            'created_at' => now(),
            'updated_at' => now(),
            'status' => StatusEnum::Accepted
        ];
    }
}
