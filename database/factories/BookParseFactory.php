<?php

use Faker\Generator as Faker;

$factory->define(App\BookParse::class, function (Faker $faker) {
	return [
		'book_id' => function () {
			return factory(App\Book::class)->create()->id;
		},
		'create_user_id' => function () {
			return factory(App\User::class)->create()->id;
		}
	];
});

$factory->afterMakingState(App\BookParse::class, 'waited', function ($book_parse, $faker) {
	$book_parse->wait();
});

$factory->afterMakingState(App\BookParse::class, 'reseted', function ($book_parse, $faker) {
	$book_parse->reset();
});

$factory->afterMakingState(App\BookParse::class, 'started', function ($book_parse, $faker) {
	$book_parse->start();
});

$factory->afterMakingState(App\BookParse::class, 'successed', function ($book_parse, $faker) {
	$book_parse->success();
});

$factory->afterMakingState(App\BookParse::class, 'failed', function ($book_parse, $faker) {

	$error = [
		'message' => 'Message',
		'code' => '1',
		'file' => '/file.php',
		'line' => '2',
		'traceAsString' => ''
	];

	$book_parse->failed($error);
});

$factory->afterMakingState(App\BookParse::class, 'only_pages', function ($book_parse, $faker) {
	$book_parse->parseOnlyPages();
});


