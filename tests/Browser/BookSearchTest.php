<?php

namespace Tests\Browser;

use App\Book;
use App\BookKeyword;
use App\Genre;
use Illuminate\Support\Str;
use Tests\DuskTestCase;

class BookSearchTest extends DuskTestCase
{
	/**
	 * A Dusk test example.
	 *
	 * @return void
	 */

	public function testSelectGenres()
	{
		$this->browse(function ($user_browser) {

			$genre = Genre::factory()->with_main_genre()->create();

			$genre_group = $genre->group;

			$book = Book::factory()->create(['title' => Str::random(8)]);
			$book->genres()->sync([$genre->id]);
			$book->readAccessEnable();
			$book->downloadAccessEnable();
			$book->push();

			$user_browser->resize(1500, 1500)
				->visit(route('books'))
				->click('#selected_genres_button')
				->waitFor('#selected_genres_modal')
				->waitFor('#selected_genres_modal .card-columns')
				->scrollToElement('footer')
				->with('#selected_genres_modal .card-columns', function ($container) use ($genre) {
					$container->assertSee($genre->name)
						->check('input[type=checkbox][data-type=genre][data-id="' . $genre->id . '"]')
						->assertChecked('input[type=checkbox][data-type=genre][data-id="' . $genre->id . '"]');
				})
				->click('#selected_genres_modal .close')
				->waitUntilMissing('#selected_genres_modal')
				->pause(500)
				->with('select.genres ~ .select2-container', function ($container) use ($genre) {
					$container->assertSee($genre->name);
				})
				->with('.books-search-container', function ($container) use ($book) {
					$container->type('search', $book->title)
						->waitFor('.loading-cap')
						->waitUntilMissing('.loading-cap')
						->assertSee($book->title);
				});

			$genre->delete();
			$genre_group->delete();
			$book->delete();
		});
	}

	public function testSelectExcludedGenres()
	{
		$this->browse(function ($user_browser) {

			$genre = Genre::factory()->with_main_genre()->create();

			$genre_group = $genre->group;

			$book = Book::factory()->create(['title' => uniqid()]);
			$book->genres()->sync([$genre->id]);
			$book->push();

			$user_browser->resize(1500, 1500)
				->visit(route('books'))
				->with('.books-search-container', function ($container) use ($book) {
					$container->type('search', $book->title)
						->waitForText($book->title, 15)
						->assertSee($book->title);
				})
				->click('#excluded_genres_button')
				->waitFor('#excluded_genres_modal')
				->waitFor('#excluded_genres_modal .card-columns')
				->scrollToElement('footer')
				->with('#excluded_genres_modal .card-columns', function ($container) use ($genre) {
					$container->assertSee($genre->name)
						->check('input[type=checkbox][data-type=genre][data-id="' . $genre->id . '"]')
						->assertChecked('input[type=checkbox][data-type=genre][data-id="' . $genre->id . '"]');
				})
				->click('#excluded_genres_modal .close')
				->waitUntilMissing('#excluded_genres_modal')
				->pause(500)
				->with('select.exclude-genres ~ .select2-container', function ($container) use ($genre) {
					$container->assertSee($genre->name);
				})
				->with('.books-search-container', function ($container) use ($book) {
					$container->type('search', $book->title)
						->waitForText(__('book.nothing_found'))
						->assertDontSee($book->title);
				});

			$genre->delete();
			$genre_group->delete();
			$book->delete();
		});
	}

	public function testSelectedKeywords()
	{
		$this->browse(function ($user_browser) {

			$book_keyword = BookKeyword::factory()->create();

			$book = $book_keyword->book;
			$keyword = $book_keyword->keyword;

			$user_browser->resize(1500, 1500)
				->visit(route('books', ['kw' => $keyword->text]))
				->assertSelectHasOption('kw[]', $keyword->text);
		});
	}
}
