<?php

namespace Database\Factories;

use App\SearchQueriesLog;
use Illuminate\Support\Str;

class SearchQueriesLogsFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = SearchQueriesLog::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'query_text' => Str::random(10)
        ];
    }
}
