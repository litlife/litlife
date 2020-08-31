<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\SearchQueriesLogs;
use Faker\Generator as Faker;

$factory->define(SearchQueriesLogs::class, function (Faker $faker) {
	return [
		'query_text' => \Illuminate\Support\Str::random(10)
	];
});
