<?php

use App\BookGroup;
use Faker\Generator as Faker;

$factory->define(App\BookGroup::class, function (Faker $faker) {
	return [
		'create_user_id' => function () {
			return factory(App\User::class)->create()->id;
		}
	];
});

$factory->afterCreatingState(App\BookGroup::class, 'with_one_book', function (BookGroup $group, Faker $faker) {

	$book = factory(\App\Book::class)->create();
	$book->addToGroup($group);
	$book->save();

	$group->refreshBooksCount();
	$group->save();
});

$factory->afterCreatingState(App\BookGroup::class, 'add_two_books', function (BookGroup $group, Faker $faker) {

	$book = factory(\App\Book::class)->create();
	$book->addToGroup($group);
	$book->save();

	$book2 = factory(\App\Book::class)->create();
	$book2->addToGroup($group);
	$book2->save();

	$group->refreshBooksCount();
	$group->save();
});

$factory->afterCreatingState(App\BookGroup::class, 'with_main_book', function (BookGroup $group, Faker $faker) {

	$book = factory(\App\Book::class)->create();
	$book->addToGroup($group, true);
	$book->save();

	$group->refreshBooksCount();
	$group->save();
});


