<?php

/** @var Factory $factory */

use App\SearchQueriesLogs;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

$factory->define(SearchQueriesLogs::class, function (Faker $faker) {
    return [
        'query_text' => \Illuminate\Support\Str::random(10)
    ];
});
