<?php

use App\AuthorBiography;
use App\AuthorPhoto;
use App\BookVote;
use App\Comment;
use App\Enums\Gender;
use App\Enums\StatusEnum;
use App\Jobs\Author\UpdateAuthorCommentsCount;
use App\Jobs\Author\UpdateAuthorRating;
use App\Language;
use App\Manager;
use App\User;
use App\UserPurchase;
use Faker\Generator as Faker;

$factory->define(App\Author::class, function (Faker $faker) {
	return [
		'nickname' => preg_replace('/(\'|\"|ё)/iu', '', $faker->userName),
		'last_name' => preg_replace('/(\'|\"|ё)/iu', '', $faker->lastName),
		'first_name' => preg_replace('/(\'|\"|ё)/iu', '', $faker->firstName),
		'middle_name' => preg_replace('/(\'|\"|ё)/iu', '', $faker->text(rand(5, 10))),
		'lang' => function () {
			return Language::inRandomOrder()->first()->code;
		},
		'home_page' => $faker->url,
		'email' => $faker->unique()->safeEmail,
		'create_user_id' => function () {
			return factory(App\User::class)->create()->id;
		},
		'wikipedia_url' => 'http://ru.wikipedia.org/wiki/' . $faker->text(rand(5, 20)),
		'born_date' => $faker->date('d M Y', '-20 years'),
		'born_place' => $faker->city,
		'dead_date' => $faker->date('d M Y'),
		'dead_place' => $faker->city,
		'years_creation' => $faker->date('Y') . '-' . $faker->date('Y'),
		'orig_last_name' => $faker->lastName,
		'orig_first_name' => $faker->firstName,
		'orig_middle_name' => $faker->lastName,
		'gender' => Gender::getRandomKey(),
		'status' => StatusEnum::Accepted,
		'status_changed_at' => now(),
		'status_changed_user_id' => rand(50000, 100000)
	];
});

$factory->afterCreatingState(App\Author::class, 'with_biography', function ($author, $faker) {

	$biography = factory(AuthorBiography::class)
		->create(['author_id' => $author->id]);
});

$factory->afterCreatingState(App\Author::class, 'with_book', function ($author, $faker) {

	$book = factory(\App\Book::class)
		->states('with_section', 'complete', 'with_genre')
		->create();

	$book->writers()->detach();
	$book->writers()->attach([$author->id]);

	$author->refresh();
});

$factory->afterCreatingState(App\Author::class, 'with_si_book', function ($author, $faker) {

	$book = factory(\App\Book::class)
		->states('with_section', 'si_true', 'complete', 'with_genre')
		->create();

	$book->writers()->detach();
	$book->writers()->attach([$author->id]);

	$author->refresh();
});

$factory->afterCreatingState(App\Author::class, 'with_complete_book', function ($author, $faker) {

	$book = factory(\App\Book::class)
		->states('with_section', 'complete')
		->create();

	$book->writers()->detach();
	$book->writers()->attach([$author->id]);

	$author->refresh();
});

$factory->afterCreatingState(App\Author::class, 'with_book_cover_annotation', function ($author, $faker) {

	$book = factory(\App\Book::class)
		->states('with_cover', 'with_section', 'with_annotation', 'complete', 'with_genre')
		->create();

	$book->writers()->detach();
	$book->writers()->attach([$author->id]);

	$author->refresh();
});

$factory->afterCreatingState(App\Author::class, 'with_private_book', function ($author, $faker) {

	$book = factory(\App\Book::class)
		->states('with_section', 'private', 'with_genre')
		->create();

	$book->writers()->detach();
	$book->writers()->attach([$author->id]);
	$author->refresh();
});

$factory->afterCreatingState(App\Author::class, 'with_paid_section', function ($author, $faker) {

	$book = factory(\App\Book::class)
		->state('with_paid_section')
		->create();

	$book->writers()->syncWithoutDetaching([$author->id]);
	$author->refresh();
});

$factory->afterCreatingState(App\Author::class, 'with_book_for_sale', function ($author, $faker) {

	$book = factory(\App\Book::class)
		->states('with_section', 'without_any_authors', 'complete', 'with_cover', 'with_annotation', 'with_genre')
		->create([
			'price' => rand(50, 100),
			'is_si' => true,
			'is_lp' => false,
			'pi_pub' => null,
			'pi_city' => null,
			'pi_year' => null,
			'pi_isbn' => null
		]);

	$book->characters_count = config('litlife.minimum_characters_count_before_book_can_be_sold') + 1000;
	$book->save();

	$book->writers()->syncWithoutDetaching([$author->id]);

	unset($book->writers);
});

$factory->afterCreatingState(App\Author::class, 'with_book_for_sale_purchased', function ($author, $faker) {

	$purchase = factory(UserPurchase::class)
		->state('book')
		->create();

	$book = $purchase->purchasable;
	$book->is_si = true;
	$book->is_lp = false;
	$book->price = rand(50, 100);
	$book->ready_status = 'complete';
	$book->save();

	$book->writers()->syncWithoutDetaching([$author->id]);
	$author->refresh();

	$purchase->buyer->purchasedBookCountRefresh();
	$book->boughtTimesCountRefresh();
});

$factory->afterCreatingState(App\Author::class, 'with_book_removed_from_sale', function ($author, $faker) {

	$book = factory(\App\Book::class)
		->states('with_section', 'without_any_authors', 'removed_from_sale')
		->create(['price' => rand(50, 100)]);

	$book->writers()->syncWithoutDetaching([$author->id]);
	$author->refresh();
});

$factory->afterCreatingState(App\Author::class, 'with_book_vote', function ($author, $faker) {

	$book = factory(\App\Book::class)
		->create();

	$book->writers()->syncWithoutDetaching([$author->id]);

	$book_vote = factory(BookVote::class)
		->create(['book_id' => $book->id]);

	UpdateAuthorRating::dispatch($author);
	\App\Jobs\Author\UpdateAuthorBooksCount::dispatch($author);
});

$factory->afterCreatingState(App\Author::class, 'with_book_comment', function ($author, $faker) {

	$book = factory(\App\Book::class)
		->create();

	$book->writers()->syncWithoutDetaching([$author->id]);

	$book_vote = factory(Comment::class)
		->create(['commentable_type' => 'book', 'commentable_id' => $book->id]);

	UpdateAuthorCommentsCount::dispatch($author);
});

$factory->afterCreatingState(App\Author::class, 'with_translated_book', function ($author, $faker) {

	$book = factory(\App\Book::class)
		->create();

	$author->translated_books()->syncWithoutDetaching([$book->id]);
	$author->refresh();
});

$factory->afterCreatingState(App\Author::class, 'with_illustrated_book', function ($author, $faker) {

	$book = factory(\App\Book::class)
		->create();

	$author->illustrated_books()->syncWithoutDetaching([$book->id]);
	$author->refresh();
});

$factory->afterCreatingState(App\Author::class, 'with_edited_book', function ($author, $faker) {

	$book = factory(\App\Book::class)
		->create();

	$author->edited_books()->syncWithoutDetaching([$book->id]);
	$author->refresh();
});

$factory->afterCreatingState(App\Author::class, 'with_compiled_book', function ($author, $faker) {

	$book = factory(\App\Book::class)
		->create();

	$author->compiled_books()->syncWithoutDetaching([$book->id]);
	$author->refresh();
});

$factory->afterCreatingState(App\Author::class, 'with_author_manager', function ($author, $faker) {

	$manager = factory(Manager::class)
		->create([
			'character' => 'author'
		]);

	$author->managers()->save($manager);
	$author->refresh();
});

$factory->afterCreatingState(App\Author::class, 'with_author_manager_on_review', function ($author, $faker) {

	$manager = factory(Manager::class)
		->states('author', 'on_review')
		->create([
			'character' => 'author'
		]);

	$author->managers()->save($manager);
	$author->refresh();
});

$factory->afterCreatingState(App\Author::class, 'with_author_manager_can_sell', function ($author, $faker) {

	$manager = factory(Manager::class)
		->create([
			'character' => 'author',
			'can_sale' => true
		]);

	$author->managers()->save($manager);
	$author->refresh();
});

$factory->afterCreatingState(App\Author::class, 'with_editor_manager', function ($author, $faker) {

	$manager = factory(Manager::class)
		->states('character_editor')
		->create();

	$author->managers()->save($manager);
	$author->refresh();
});

$factory->afterCreatingState(App\Author::class, 'with_seller_refered', function ($author, $faker) {

	$referer = factory(User::class)
		->create();

	$seller = $author->seller();
	$seller->referred_by_user()->associate($referer);
	$seller->save();
});

$factory->afterCreatingState(App\Author::class, 'with_two_managers_and_one_can_sell', function ($author, $faker) {

	$manager = factory(Manager::class)
		->create([
			'character' => 'author',
			'can_sale' => true
		]);

	$author->managers()->save($manager);

	$manager = factory(Manager::class)
		->create([
			'character' => 'author',
			'can_sale' => false
		]);

	$author->managers()->save($manager);

	$author->refresh();
});

$factory->afterMakingState(App\Author::class, 'private', function (\App\Author $author, $faker) {
	$author->statusPrivate();
});

$factory->afterMakingState(App\Author::class, 'accepted', function (\App\Author $author, $faker) {
	$author->statusAccepted();
});

$factory->afterCreatingState(App\Author::class, 'with_photo', function (\App\Author $author, $faker) {

	$photo = factory(AuthorPhoto::class)
		->create([
			'author_id' => $author->id
		]);

	$author->photo_id = $photo->id;
	$author->save();
});