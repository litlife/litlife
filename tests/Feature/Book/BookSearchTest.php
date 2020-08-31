<?php

namespace Tests\Feature\Book;

use App\Attachment;
use App\Award;
use App\Book;
use App\BookAward;
use App\BookFile;
use App\BookKeyword;
use App\BookStatus;
use App\BookVote;
use App\Enums\StatusEnum;
use App\Genre;
use App\Jobs\Book\BookGroupJob;
use App\Section;
use App\User;
use Illuminate\Support\Str;
use Tests\TestCase;

class BookSearchTest extends TestCase
{
	/**
	 * A basic test example.
	 *
	 * @return void
	 */
	public function testPrivacyInSearch()
	{
		$book = factory(Book::class)->create([
			'title' => uniqid() . uniqid() . uniqid(),
			'status' => StatusEnum::Private
		]);

		$book = Book::any()->findOrFail($book->id);

		$other_user = factory(User::class)->create();

		$this->actingAs($book->create_user)
			->get(route('books', ['search' => $book->title, 'order' => 'date_down']))
			->assertSeeText($book->title);

		$this->actingAs($other_user)
			->get(route('books', ['search' => $book->title, 'order' => 'date_down']))
			->assertDontSeeText($book->title)
			->assertDontSeeText(__('book.deleted'))
			->assertSeeText(__('book.nothing_found'));
	}

	public function testDeletedInSearch()
	{
		$book = factory(Book::class)->create([
			'title' => uniqid() . uniqid() . uniqid(),
			'status' => StatusEnum::Private
		]);

		$book = Book::any()->findOrFail($book->id);

		$book->delete();

		$this->actingAs($book->create_user)
			->get(route('books', ['search' => $book->title, 'order' => 'date_down']))
			->assertDontSeeText($book->title)
			->assertDontSeeText(__('book.deleted'))
			->assertSeeText(__('book.nothing_found'));
	}

	public function testGenre()
	{
		$book = factory(Book::class)->create([
			'title' => uniqid() . uniqid(),
			'status' => StatusEnum::Accepted
		])->fresh();

		$this->assertEquals(1, $book->genres()->count());

		$this->actingAs($book->create_user)
			->get(route('books', ['genre' => $book->genres()->first()->id, 'order' => 'date_down']))
			->assertSeeText($book->title)
			->assertDontSeeText(__('book.nothing_found'));
	}

	public function testExcludeGenres()
	{
		$title = Str::random(10);

		$book = factory(Book::class)->states('accepted')->create([
			'title' => $title
		])->fresh();

		$this->assertEquals(1, $book->genres()->count());

		$this->actingAs($book->create_user)
			->get(route('books', ['search' => $book->title, 'genre' => $book->genres()->first()->id, 'order' => 'date_down']))
			->assertSeeText($title)
			->assertDontSeeText(__('book.nothing_found'));

		sleep(1);

		$this->actingAs($book->create_user)
			->get(route('books', ['search' => $book->title, 'exclude_genres' => $book->genres()->first()->id, 'order' => 'date_down']))
			->assertSeeText(__('book.nothing_found'));
	}

	public function testLanguage()
	{

		$book = factory(Book::class)->create([
			'title' => uniqid() . uniqid(),
			'ti_lb' => 'EN',
			'status' => StatusEnum::Accepted
		])->fresh();

		$this->actingAs($book->create_user)
			->get(route('books', ['search' => $book->title, 'language' => 'EN', 'order' => 'date_down']))
			->assertSeeText($book->title)
			->assertDontSeeText(__('book.nothing_found'));

		$this->actingAs($book->create_user)
			->get(route('books', ['search' => $book->title, 'language' => 'RU', 'order' => 'date_down']))
			->assertSeeText(__('book.nothing_found'));

		$book->ti_olb = 'RU';
		$book->save();

		$this->get(route('books', ['search' => $book->title, 'originalLang' => 'RU', 'order' => 'date_down']))
			->assertSeeText($book->title)
			->assertDontSeeText(__('book.nothing_found'));

		$this->get(route('books', ['search' => $book->title, 'originalLang' => 'EN', 'order' => 'date_down']))
			->assertSeeText(__('book.nothing_found'));

	}

	public function testKeywords()
	{
		$book = factory(Book::class)->create([
			'title' => uniqid() . uniqid(),
			'status' => StatusEnum::Accepted
		])->fresh();

		$book_keyword = factory(BookKeyword::class)->create([
			'book_id' => $book->id
		]);

		$this->actingAs($book->create_user)
			->get(route('books', ['search' => $book->title, 'kw' => $book_keyword->keyword->text, 'order' => 'date_down']))
			->assertOk()
			->assertSeeText($book->title)
			->assertDontSeeText(__('book.nothing_found'));

		$this->actingAs($book->create_user)
			->get(route('books', ['search' => $book->title, 'kw' => uniqid(), 'order' => 'date_down']))
			->assertOk()
			->assertDontSeeText(__('book.nothing_found'));
	}

	public function testSearchKeywordWrongEncoding()
	{
		$this->get(route('books') . '?&kw%5B0%5D=%D0%96%D0%B5%D1%81%D1%82%D0%BA%D0%B8%D0%B9%20%D0%B8%20%D0%B2%D0%BB%D0%B0%D1%81%D1%82%D0%BD%D1')
			->assertOk();

		$this->get(route('books', ['search' => mb_convert_encoding('тест', 'windows-1251')]))
			->assertOk();
	}

	/*
		public function testViewGrouped()
		{
			$book = factory(Book::class)->create([
				'title' => uniqid() . uniqid(),
				'status' => StatusEnum::Accepted
			]);

			$book2 = factory(Book::class)->create([
				'title' => uniqid() . uniqid(),
				'status' => StatusEnum::Accepted
			]);

			$group = factory(BookGroup::class)
				->create();

			$book->addToGroup($group, true);
			$book->save();
			$book->refresh();

			$book2->addToGroup($group, false);
			$book2->save();
			$book2->refresh();

			$this->assertTrue($book->isInGroup());
			$this->assertTrue($book->isMainInGroup());

			$this->assertTrue($book2->isInGroup());
			$this->assertTrue($book2->isNotMainInGroup());

			$this->get(route('books', ['search' => $book->title, 'order' => 'date_down', 'hide_grouped' => '1']))
				->assertSeeText($book->title)
				->assertDontSeeText(__('book.nothing_found'));

			$this->get(route('books', ['search' => $book2->title, 'order' => 'date_down', 'hide_grouped' => '1']))
				->assertDontSeeText($book2->title)
				->assertSeeText(__('book.nothing_found'));
		}
		*/

	public function testFormats()
	{
		$book = factory(Book::class)->create([
			'title' => uniqid() . uniqid(),
			'status' => StatusEnum::Accepted
		])->fresh();

		$book_file = factory(BookFile::class)
			->states('odt')
			->create([
				'book_id' => $book->id,
				'status' => StatusEnum::Accepted
			]);

		$this->actingAs($book->create_user)
			->get(route('books', ['search' => $book->title, 'Formats' => 'odt', 'order' => 'date_down']))
			->assertSeeText($book->title)
			->assertDontSeeText(__('book.nothing_found'));

		$this->actingAs($book->create_user)
			->get(route('books', ['search' => $book->title, 'Formats' => 'doc', 'order' => 'date_down']))
			->assertSeeText(__('book.nothing_found'));

	}

	public function testBookBookCompleteStatus()
	{
		$ready_status = 'complete';

		$book = factory(Book::class)->create([
			'title' => uniqid() . uniqid(),
			'ready_status' => $ready_status
		])->fresh();

		$this->actingAs($book->create_user)
			->get(route('books', ['search' => $book->title, 'rs' => $ready_status, 'order' => 'date_down']))
			->assertSeeText($book->title)
			->assertDontSeeText(__('book.nothing_found'));

		$this->actingAs($book->create_user)
			->get(route('books', ['search' => $book->title, 'rs' => 'complete_but_publish_only_part', 'order' => 'date_down']))
			->assertSeeText(__('book.nothing_found'));

	}

	public function testAuthorGender()
	{
		$book = factory(Book::class)
			->states('with_writer', 'with_translator')
			->create([
				'title' => uniqid() . uniqid()
			])->fresh();

		$author = $book->writers()->first();
		$author->gender = 'female';
		$author->save();

		$author = $book->translators()->first();
		$author->gender = 'male';
		$author->save();

		$this->actingAs($book->create_user)
			->get(route('books', ['search' => $book->title, 'author_gender' => 'female', 'order' => 'date_down']))
			->assertSeeText($book->title)
			->assertDontSeeText(__('book.nothing_found'));

		$this->actingAs($book->create_user)
			->get(route('books', ['search' => $book->title, 'author_gender' => 'male', 'order' => 'date_down']))
			->assertSeeText(__('book.nothing_found'));

		$author = $book->writers()->first();
		$author->gender = 'male';
		$author->save();

		$author = $book->translators()->first();
		$author->gender = 'female';
		$author->save();

		$this->actingAs($book->create_user)
			->get(route('books', ['search' => $book->title, 'author_gender' => 'male', 'order' => 'date_down']))
			->assertSeeText($book->title)
			->assertDontSeeText(__('book.nothing_found'));

		$this->actingAs($book->create_user)
			->get(route('books', ['search' => $book->title, 'author_gender' => 'female', 'order' => 'date_down']))
			->assertSeeText(__('book.nothing_found'));
	}

	public function testReadLaterAndVoteAverage()
	{
		$book_status = factory(BookStatus::class)
			->create(['status' => 'readed']);

		$book_vote = factory(BookVote::class)
			->create(['book_id' => $book_status->book->id]);

		$this->actingAs($book_status->user)
			->get(route('users.books.readed', ['user' => $book_status->user->id]))
			->assertOk()
			->assertSeeText($book_status->book->title);

		$this->actingAs($book_status->user)
			->get(route('users.books.readed',
				['user' => $book_status->user->id, 'order' => 'rating_week_desc']))
			->assertOk();

		$this->actingAs($book_status->user)
			->get(route('users.books.readed',
				['user' => $book_status->user->id, 'order' => 'rating_month_desc']))
			->assertOk();

		$this->actingAs($book_status->user)
			->get(route('users.books.readed',
				['user' => $book_status->user->id, 'order' => 'rating_quarter_desc']))
			->assertOk();

		$this->actingAs($book_status->user)
			->get(route('users.books.readed',
				['user' => $book_status->user->id, 'order' => 'rating_year_desc']))
			->assertOk();
	}

	public function testGenres()
	{
		$book = factory(Book::class)
			->create();

		$genre = factory(Genre::class)
			->create();

		$book->genres()->sync([$genre->id]);
		$book->push();
		$book->refresh();

		$this->assertEquals(1, $book->genres()->count());

		$this->get(route('books', ['genre' => $genre->id . ',11']))
			->assertOk()
			->assertSeeText($book->title);

		$this->get(route('books', ['genre' => $genre->id]))
			->assertOk()
			->assertSeeText($book->title);

		$this->get(route('books', ['genre' => [$genre->id]]))
			->assertOk()
			->assertSeeText($book->title);
	}

	public function testMainGenres()
	{
		$book = factory(Book::class)
			->create();

		$genre = factory(Genre::class)->states('with_main_genre')->create();
		$book->genres()->sync([$genre->id]);
		$book->push();

		$mainGenre = $genre->group;

		$this->get(route('genres.show', ['genre' => $mainGenre->id]))
			->assertOk()
			->assertSeeText($book->title);
	}

	public function testHideGroupedOptionWithDefaultValue()
	{
		$search = Str::random(6);

		$mainBook = factory(Book::class)
			->create(['title' => $search . ' ' . Str::random(8)]);

		$minorBook = factory(Book::class)
			->create(['title' => $search . ' ' . Str::random(8)]);

		BookGroupJob::dispatch($mainBook, $minorBook);

		$this->get(route('books', ['search' => $search]))
			->assertOk()
			->assertSeeText($mainBook->title)
			->assertDontSeeText($minorBook->title);

		$this->get(route('books', ['search' => $search, 'hide_grouped' => 0]))
			->assertOk()
			->assertSeeText($mainBook->title)
			->assertSeeText($minorBook->title);

		$this->get(route('books', ['search' => $search, 'hide_grouped' => 1]))
			->assertOk()
			->assertSeeText($mainBook->title)
			->assertDontSeeText($minorBook->title);
	}

	public function testReadAccessDefaultOption()
	{
		$search = Str::random(6);

		$openedBook = factory(Book::class)->create(['title' => $search . ' ' . Str::random(8)]);
		$openedBook->readAccessEnable();
		$openedBook->save();

		$closedBook = factory(Book::class)->create(['title' => $search . ' ' . Str::random(8)]);
		$closedBook->readAccessDisable();
		$closedBook->save();

		$this->get(route('books', ['search' => $search]))
			->assertOk()
			->assertSeeText($openedBook->title)
			->assertDontSeeText($closedBook->title);

		$this->get(route('books', ['search' => $search, 'read_access' => 'any']))
			->assertOk()
			->assertSeeText($openedBook->title)
			->assertSeeText($closedBook->title);

		$this->get(route('books', ['search' => $search, 'read_access' => 'open']))
			->assertOk()
			->assertSeeText($openedBook->title)
			->assertDontSeeText($closedBook->title);

		$this->get(route('books', ['search' => $search, 'read_access' => 'close']))
			->assertOk()
			->assertDontSeeText($openedBook->title)
			->assertSeeText($closedBook->title);
	}

	public function testCoverExists()
	{
		$book = factory(Book::class)
			->create(['title' => uniqid()]);

		$this->assertNull($book->cover);

		$this->get(route('books', ['search' => $book->title, 'CoverExists' => 'no']))
			->assertOk()
			->assertDontSee(__('book.nothing_found'));

		$this->get(route('books', ['search' => $book->title, 'CoverExists' => 'yes']))
			->assertOk()
			->assertSee(__('book.nothing_found'));

		$attachment = factory(Attachment::class)->create();
		$book->cover()->associate($attachment);
		$book->save();

		$this->assertNotNull($book->cover);

		$this->get(route('books', ['search' => $book->title, 'CoverExists' => 'no']))
			->assertOk()
			->assertSee(__('book.nothing_found'));

		$this->get(route('books', ['search' => $book->title, 'CoverExists' => 'yes']))
			->assertOk()
			->assertDontSee(__('book.nothing_found'));
	}

	public function testAnnotationExists()
	{
		$book = factory(Book::class)
			->create(['title' => uniqid()]);

		$this->assertFalse($book->fresh()->annotation_exists);

		$this->get(route('books', ['search' => $book->title, 'AnnotationExists' => 'no']))
			->assertOk()
			->assertDontSee(__('book.nothing_found'));

		$this->get(route('books', ['search' => $book->title, 'AnnotationExists' => 'yes']))
			->assertOk()
			->assertSee(__('book.nothing_found'));

		$annotation = factory(Section::class)
			->create(['type' => 'annotation', 'book_id' => $book->id]);

		$this->assertTrue($book->fresh()->annotation_exists);

		$this->get(route('books', ['search' => $book->title, 'AnnotationExists' => 'yes']))
			->assertOk()
			->assertDontSee(__('book.nothing_found'));

		$this->get(route('books', ['search' => $book->title, 'AnnotationExists' => 'no']))
			->assertOk()
			->assertSee(__('book.nothing_found'));
	}

	public function testYear()
	{
		$value = 1000000;

		$this->get(route('books', ['write_year_after' => $value]))
			->assertOk();

		$this->get(route('books', ['write_year_before' => $value]))
			->assertOk();

		$this->get(route('books', ['publish_year_after' => $value]))
			->assertOk();

		$this->get(route('books', ['publish_year_before' => $value]))
			->assertOk();
	}

	public function testPageCount()
	{
		$value = 10000000000000000000;

		$this->get(route('books', ['pages_count_min' => $value]))
			->assertOk();

		$this->get(route('books', ['pages_count_max' => $value]))
			->assertOk();
	}

	public function testOrderArrayFix()
	{
		$response = $this->get(route('books', ['order' => ['rating_avg_down', 'rating_avg_down']]))
			->assertOk();

		$view_data = $response->getOriginalContent()->getData();

		$this->assertEquals('rating_avg_down', $view_data['input']['order']);

		$response = $this->get(route('books', ['order' => ['rating_avg_up', 'rating_avg_down']]))
			->assertOk();

		$view_data = $response->getOriginalContent()->getData();

		$this->assertEquals('rating_avg_up', $view_data['input']['order']);
	}

	public function testViewHasOrder()
	{
		$response = $this->get(route('books', ['order' => 'rating_avg_up']))
			->assertOk();

		$view_data = $response->getOriginalContent()->getData();

		$this->assertEquals('rating_avg_up', $view_data['input']['order']);
	}

	public function testEscapeCharactersInPublishCity()
	{
		$response = $this->get(route('books', ['publish_city' => '\\']))
			->assertOk();
	}

	public function testPublishCity()
	{
		$title = Str::random(8);
		$publish_city = Str::random(8);

		$book = factory(Book::class)
			->create(['title' => $title, 'pi_city' => $publish_city]);

		$response = $this->get(route('books', ['publish_city' => $publish_city]))
			->assertOk()
			->assertDontSeeText(__('book.nothing_found'))
			->assertSeeText($book->title);
	}

	public function testEscapeCharactersInTitle()
	{
		$response = $this->get(route('books', ['title' => '\\']))
			->assertOk();
	}

	public function testUserReadStatus()
	{
		$title = Str::random(8);

		$book = factory(Book::class)
			->create(['title' => $title]);

		$user = factory(User::class)
			->create();

		$this->actingAs($user)
			->get(route('books', ['search' => $title]))
			->assertOk()
			->assertDontSeeText(__('book.nothing_found'))
			->assertSeeText($book->title);

		$bookStatus = factory(BookStatus::class)
			->states('readed')
			->create([
				'book_id' => $book->id,
				'user_id' => $user->id,
			]);

		$this->actingAs($user)
			->get(route('books', ['search' => $title, 'read_status' => 'readed']))
			->assertOk()
			->assertDontSeeText(__('book.nothing_found'))
			->assertSeeText($book->title);

		$this->actingAs($user)
			->get(route('books', ['search' => $title, 'read_status' => 'read_now']))
			->assertOk()
			->assertSeeText(__('book.nothing_found'))
			->assertDontSeeText($book->title);

		$this->actingAs($user)
			->get(route('books', ['search' => $title, 'read_status' => 'no_status']))
			->assertOk()
			->assertSeeText(__('book.nothing_found'))
			->assertDontSeeText($book->title);
	}

	public function testGenreLongIntegerHttpOk()
	{
		$this->get(route('books', ['genre' => '1022121121121212.1']))
			->assertOk();
	}

	public function testExcludeGenreLongIntegerHttpOk()
	{
		$this->get(route('books', ['exclude_genres' => '1022121121121212.1']))
			->assertOk();
	}

	public function testStatusOfPublicationFilter()
	{
		$title = Str::random(10);

		$book = factory(Book::class)
			->states('private')
			->create(['title' => $title]);

		$user = $book->create_user;

		$this->get(route('books', ['search' => $title]))
			->assertOk()
			->assertDontSeeText(__('book.search.status_of_publication'));

		$this->actingAs($user)
			->get(route('books', ['search' => $title, 'status_of_publication' => 'private_books_only']))
			->assertOk()
			->assertSeeText(__('book.search.status_of_publication'))
			->assertSeeText($title)
			->assertDontSeeText(__('book.nothing_found'));

		$this->actingAs($user)
			->get(route('books', ['search' => $title, 'status_of_publication' => 'published_books_only']))
			->assertOk()
			->assertSeeText(__('book.nothing_found'))
			->assertDontSeeText($title);

		$book->statusAccepted();
		$book->save();

		$this->actingAs($user)
			->get(route('books', ['search' => $title, 'status_of_publication' => 'private_books_only']))
			->assertOk()
			->assertSeeText(__('book.nothing_found'))
			->assertDontSeeText($title);

		$this->actingAs($user)
			->get(route('books', ['search' => $title, 'status_of_publication' => 'published_books_only']))
			->assertOk()
			->assertSeeText($title)
			->assertDontSeeText(__('book.nothing_found'));
	}

	public function testPerPage()
	{
		$response = $this->get(route('books', ['per_page' => 5]))
			->assertOk();

		$this->assertEquals(10, $response->original->gatherData()['books']->perPage());

		$response = $this->get(route('books', ['per_page' => 200]))
			->assertOk();

		$this->assertEquals(100, $response->original->gatherData()['books']->perPage());
	}

	public function testRealLaterAndAward()
	{
		$user = factory(User::class)
			->create();

		$award = factory(Award::class)
			->create(['title' => Str::random(8)]);

		$bookStatus = factory(BookStatus::class)
			->states('readed')
			->create(['user_id' => $user->id]);

		$book = $bookStatus->book;
		$book->title = Str::random(8);
		$book->save();

		$bookAward = factory(BookAward::class)
			->create(['book_id' => $book->id, 'award_id' => $award->id]);

		$this->actingAs($user)
			->get(route('users.books.readed', ['user' => $user, 'award' => $award->title, 'order' => 'last_status_change']))
			->assertOk()
			->assertSeeText($book->title);
	}

	public function testPublishYearExactValue()
	{
		$title = Str::random(8);

		$book = factory(Book::class)
			->states('accepted')
			->create(['title' => $title, 'pi_year' => 2020]);

		$this->get(route('books', [
			'search' => $title,
			'publish_year_after' => '2020',
			'publish_year_before' => '2020'
		]))
			->assertOk()
			->assertSeeText($book->title);
	}

	public function testPublishYearAfter()
	{
		$book = factory(Book::class)
			->states('accepted')
			->create(['title' => Str::random(8), 'pi_year' => 2021]);

		$this->get(route('books', [
			'search' => $book->title,
			'publish_year_after' => '2020'
		]))
			->assertOk()
			->assertSeeText($book->title);

		$book2 = factory(Book::class)
			->states('accepted')
			->create(['title' => Str::random(8), 'pi_year' => 2019]);

		$this->get(route('books', [
			'search' => $book2->title,
			'publish_year_after' => '2020'
		]))
			->assertOk()
			->assertDontSeeText($book2->title);
	}

	public function testPublishYearBefore()
	{
		$book = factory(Book::class)
			->states('accepted')
			->create(['title' => Str::random(8), 'pi_year' => 2021]);

		$this->get(route('books', [
			'search' => $book->title,
			'publish_year_before' => '2020'
		]))
			->assertOk()
			->assertDontSeeText($book->title);

		$book2 = factory(Book::class)
			->states('accepted')
			->create(['title' => Str::random(8), 'pi_year' => 2019]);

		$this->get(route('books', [
			'search' => $book2->title,
			'publish_year_before' => '2020'
		]))
			->assertOk()
			->assertSeeText($book2->title);
	}

	public function testWriteYearExactValue()
	{
		$title = Str::random(8);

		$book = factory(Book::class)
			->states('accepted')
			->create(['title' => $title, 'year_writing' => 2018]);

		$this->get(route('books', [
			'search' => $title,
			'write_year_after' => '2018',
			'write_year_before' => '2018'
		]))
			->assertOk()
			->assertSeeText($book->title);
	}

	public function testPaidAccessSeePaidOnly()
	{
		$book = factory(Book::class)
			->states('accepted')
			->create(['title' => Str::random(8), 'price' => '50']);

		$this->get(route('books', [
			'search' => $book->title,
			'paid_access' => 'paid_only'
		]))
			->assertOk()
			->assertSeeText($book->title);

		$this->get(route('books', [
			'search' => $book->title,
			'paid_access' => 'only_free'
		]))
			->assertOk()
			->assertDontSeeText($book->title);
	}

	public function testPaidAccessSeeFreeOnly()
	{
		$book = factory(Book::class)
			->states('accepted')
			->create(['title' => Str::random(8), 'price' => '0']);

		$this->get(route('books', [
			'search' => $book->title,
			'paid_access' => 'paid_only'
		]))
			->assertOk()
			->assertDontSeeText($book->title);

		$this->get(route('books', [
			'search' => $book->title,
			'paid_access' => 'only_free'
		]))
			->assertOk()
			->assertSeeText($book->title);
	}

	public function testHideGroupedArray()
	{
		$title = Str::random(8);
		$title2 = Str::random(8);

		$mainBook = factory(Book::class)
			->states('with_minor_book')
			->create(['title' => $title]);

		$minorBook = $mainBook->groupedBooks()->first();
		$minorBook->title = $title2;
		$minorBook->save();

		$this->get(route('books', [
			'search' => $title,
			'hide_grouped' => ['0', '1']
		]))->assertOk()
			->assertSeeText($title);

		$this->get(route('books', [
			'search' => $title2,
			'hide_grouped' => ['0', '1']
		]))->assertOk()
			->assertDontSeeText($title2);
	}

	public function testHideGrouped()
	{
		$title = Str::random(8);
		$title2 = Str::random(8);

		$mainBook = factory(Book::class)
			->states('with_minor_book')
			->create(['title' => $title]);

		$minorBook = $mainBook->groupedBooks()->first();
		$minorBook->title = $title2;
		$minorBook->save();

		$this->get(route('books', [
			'search' => $title,
			'hide_grouped' => '0'
		]))->assertOk()
			->assertSeeText($title);

		$this->get(route('books', [
			'search' => $title,
			'hide_grouped' => '1'
		]))->assertOk()
			->assertSeeText($title);

		$this->get(route('books', [
			'search' => $title2,
			'hide_grouped' => '1'
		]))->assertOk()
			->assertDontSeeText($title2);

		$this->get(route('books', [
			'search' => $title2,
			'hide_grouped' => '0'
		]))->assertOk()
			->assertSeeText($title2);
	}

	public function testAndGenreField()
	{
		$book = factory(Book::class)->create();

		$genre = factory(Genre::class)->create();
		$genre2 = factory(Genre::class)->create();

		$book->genres()->sync([$genre->id, $genre2->id]);
		$book->push();
		$book->refresh();

		$this->assertEquals(2, $book->genres()->count());

		$this->get(route('books', ['genre' => $genre->id, 'and_genres' => $genre2->id]))
			->assertOk()
			->assertSeeText($book->title);

		$this->get(route('books', ['and_genres' => $genre2->id]))
			->assertOk()
			->assertSeeText($book->title);
	}

	public function testKeywordsEmpty()
	{
		$this->get(route('books', ['kw' => []]))
			->assertOk();

		$this->get(route('books', ['kw' => null]))
			->assertOk();

		$this->get(route('books', ['kw[]' => '']))
			->assertOk();
	}
}
