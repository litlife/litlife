<?php

use App\Enums\StatusEnum;
use Faker\Generator as Faker;

$factory->define(App\Attachment::class, function (Faker $faker) {

	return [
		'book_id' => function () {
			return factory(App\Book::class)->create(['status' => StatusEnum::Private])->id;
		},
		'name' => 'test.jpg',
		'content_type' => 'image/jpeg',
		'size' => '18964',
		'type' => 'image',
		'created_at' => now(),
		'updated_at' => now(),
	];
});

$factory->afterMaking(App\Attachment::class, function ($attachment, $faker) {
	$attachment->openImage(__DIR__ . '/../../tests/Feature/images/test.jpeg');
});

$factory->afterCreating(App\Attachment::class, function ($attachment, $faker) {


});

$factory->afterCreatingState(App\Attachment::class, 'cover', function ($attachment, $faker) {
	$attachment->book->cover_id = $attachment->id;
	$attachment->push();
});