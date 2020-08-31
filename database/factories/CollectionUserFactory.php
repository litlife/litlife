<?php

/** @var Factory $factory */

use App\CollectionUser;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

$factory->define(CollectionUser::class, function (Faker $faker) {
	return [
		'collection_id' => function () {
			return factory(App\Collection::class)->create()->id;
		},
		'user_id' => function () {
			return factory(App\User::class)->create()->id;
		},
		'create_user_id' => function () {
			return factory(App\User::class)->create()->id;
		},
		'can_user_manage' => false,
		'can_edit' => false,
		'can_add_books' => false,
		'can_remove_books' => false,
		'can_edit_books_description' => false,
		'can_comment' => false
	];
});

$factory->afterCreatingState(App\CollectionUser::class, 'collection_who_can_add_me', function (CollectionUser $collectionUser, $faker) {
	$collectionUser->collection->who_can_add = 'me';
	$collectionUser->collection->save();
});

$factory->afterCreating(App\CollectionUser::class, function (CollectionUser $collectionUser, $faker) {
	$collectionUser->collection->refreshUsersCount();
	$collectionUser->collection->save();
});
