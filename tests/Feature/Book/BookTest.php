<?php

namespace Tests\Feature\Book;

use App\Attachment;
use App\Author;
use App\Book;
use App\BookFile;
use App\BookKeyword;
use App\Genre;
use App\Http\Requests\StoreBook;
use App\Jobs\Book\BookUpdateCharactersCountJob;
use App\Jobs\User\UpdateUserCreatedBooksCount;
use App\Manager;
use App\Section;
use App\User;
use App\UserPurchase;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Tests\TestCase;

class BookTest extends TestCase
{
	public function testIndexHttp()
	{
		$this->get(route('books'))
			->assertOk();
	}

	public function testFulltextSearch()
	{
		$author = Book::FulltextSearch('Время&—&детство!')->get();

		$this->assertTrue(true);
	}

	/*
		public function testDocMimeType()
		{
			dd(\Illuminate\Support\Facades\File::mimeType(__DIR__ . '/Books/wrong_mime_type.doc'));
		}
		*/

	public function testBookAutoSetAge()
	{
		$book = factory(Book::class)
			->states('with_writer', 'with_create_user', 'private')
			->create();

		$user = $book->create_user;

		$genre = factory(Genre::class)->create();
		$genre->age = 18;
		$genre->save();

		$response = $this->actingAs($user)
			->patch(route('books.update', $book),
				[
					'title' => $book->title,
					'genres' => [$genre->id],
					'writers' => $book->writers()->any()->pluck('id')->toArray(),
					'ti_lb' => 'RU',
					'ti_olb' => 'RU',
					'ready_status' => 'complete'
				]);
		//dump(session('errors'));

		$response->assertSessionHasNoErrors()
			->assertRedirect(route('books.edit', $book));

		$this->assertEquals(18, $book->fresh()->age);
	}

	public function testPolicyIfManager()
	{
		$user = factory(User::class)->create();
		$user2 = factory(User::class)->create();

		$book = factory(Book::class)
			->states('with_translator', 'with_writer')
			->create();

		$book->refresh();

		$this->assertFalse($user->can('manage', $book));
		$this->assertFalse($user2->can('manage', $book));

		$author_manager = factory(Manager::class)
			->states('character_author')
			->create([
				'user_id' => $user->id,
				'manageable_id' => $book->writers->first()->id,
				'manageable_type' => 'author'
			]);

		$author_manager->statusSentForReview();
		$author_manager->save();

		$author_manager2 = factory(Manager::class)
			->states('character_author')
			->create([
				'user_id' => $user2->id,
				'manageable_id' => $book->translators->first()->id,
				'manageable_type' => 'author'
			]);

		$author_manager2->statusSentForReview();
		$author_manager2->save();

		$book->refresh();

		$this->assertFalse($user->can('manage', $book));
		$this->assertFalse($user2->can('manage', $book));

		$author_manager->statusAccepted();
		$author_manager->save();
		$author_manager2->statusAccepted();
		$author_manager2->save();

		$book->refresh();

		$this->assertTrue($user->can('manage', $book));
		$this->assertTrue($user2->can('manage', $book));

		$author_manager->delete();
		$author_manager2->delete();

		$book->refresh();

		$this->assertFalse($user->can('manage', $book));
		$this->assertFalse($user2->can('manage', $book));
	}

	public function testPurchasedRelation()
	{
		$purchase = factory(UserPurchase::class)
			->states('book')
			->create();

		$this->assertEquals($purchase->buyer_user_id, $purchase->buyer->id);
		$this->assertEquals($purchase->seller_user_id, $purchase->seller->id);
		$this->assertEquals($purchase->purchasable_id, $purchase->buyer->purchased_books->first()->id);
	}

	public function testPurchasedBooksCount()
	{
		$purchase = factory(UserPurchase::class)
			->states('book')
			->create();

		$purchase->buyer->purchasedBookCountRefresh();
		$purchase->refresh();

		$this->assertEquals(1, $purchase->buyer->data->books_purchased_count);
	}

	public function testDiffBeetweenLastPriceChangeInDays()
	{
		$book = factory(Book::class)
			->create();

		$this->assertEquals(0, $book->getDiffBeetweenLastPriceChangeInDays());

		$book->price_updated_at = now();
		$book->save();
		$book->refresh();

		$now = now();

		config(['litlife.book_price_update_cooldown' => 14]);

		Carbon::setTestNow($now->copy()->addMinute());

		$this->assertEquals(14, $book->getDiffBeetweenLastPriceChangeInDays());

		Carbon::setTestNow($now->copy()->addHour());

		$this->assertEquals(14, $book->getDiffBeetweenLastPriceChangeInDays());

		Carbon::setTestNow($now->copy()->addDays(7)->addHour());

		$this->assertEquals(7, $book->getDiffBeetweenLastPriceChangeInDays());

		Carbon::setTestNow($now->copy()->addDays(13)->addHour());

		$this->assertEquals(1, $book->getDiffBeetweenLastPriceChangeInDays());

		Carbon::setTestNow($now->copy()->addDays(14)->addHour());

		$this->assertEquals(0, $book->getDiffBeetweenLastPriceChangeInDays());

		Carbon::setTestNow($now->copy()->addDays(16));

		$this->assertEquals(0, $book->getDiffBeetweenLastPriceChangeInDays());
	}

	public function testRefreshCounters()
	{
		$user = factory(User::class)
			->create();

		$book = factory(Book::class)
			->create();

		$this->actingAs($user)
			->get(route('books.refresh_counters', ['book' => $book]))
			->assertRedirect(route('books.show', ['book' => $book]));
	}

	public function testCharactersCount()
	{
		$book = factory(Book::class)
			->states('accepted', 'with_annotation', 'with_section', 'with_note')
			->create();

		$annotation = $book->annotation;
		$annotation->characters_count = 100;
		$annotation->save();

		$section = $book->sections()->where('type', 'section')->first();
		$section->characters_count = 100;
		$section->save();

		$note = $book->sections()->where('type', 'note')->first();
		$note->characters_count = 100;
		$note->save();

		dispatch(new BookUpdateCharactersCountJob($book));

		$this->assertEquals(100, $book->fresh()->characters_count);
	}

	public function testAuthorPolicy()
	{
		$author = factory(Author::class)
			->states('with_author_manager', 'with_book_for_sale')
			->create();

		$manager = $author->managers->first();
		$book = $author->books->first();
		$user = $manager->user;

		$this->assertTrue($user->can('author', $book));

		$user = factory(User::class)
			->create();

		$this->assertFalse($user->can('author', $book));

		$author = factory(Author::class)
			->states('with_author_manager', 'with_book_for_sale')
			->create();

		$manager = $author->managers->first();
		$user = $manager->user;

		$this->assertFalse($user->can('author', $book));
	}

	public function testUpdateUserCreatedBooksOnCreate()
	{
		$book = factory(Book::class)
			->states('with_create_user')
			->create();

		$this->assertNotNull($book->create_user);

		$user = $book->create_user;

		UpdateUserCreatedBooksCount::dispatch($book->create_user);

		$user->refresh();

		$this->assertEquals(1, $user->data->created_books_count);

		$book->delete();
		$user->refresh();
		$book->refresh();

		$this->assertTrue($book->trashed());

		$this->assertEquals(0, $user->data->created_books_count);

		$book->restore();
		$user->refresh();
		$book->refresh();

		$this->assertFalse($book->trashed());

		$this->assertEquals(1, $user->data->created_books_count);
	}

	public function testValidateName()
	{
		$store = new StoreBook();

		$validator = Validator::make(['title' => ''], $store->rules(), [], __('book'));

		$this->assertContains(__('validation.required', ['attribute' => __('book.title')]), $validator->messages()->toArray()['title']);

		$validator = Validator::make(['title' => 'Тест'], $store->rules(), [], __('book'));

		$this->assertArrayNotHasKey('title', $validator->messages()->toArray());
	}

	public function testUpdateTitleAuthorsHelper()
	{
		$title = uniqid();
		$first_name = uniqid();
		$last_name = uniqid();

		$book = factory(Book::class)
			->create(['title' => $title])
			->fresh();

		$author = factory(Author::class)
			->create([
				'first_name' => $first_name,
				'last_name' => $last_name,
				'middle_name' => '',
				'nickname' => ''
			]);

		$book->writers()->sync([$author->id]);

		$book->updateTitleAuthorsHelper();
		$book->save();
		$book->refresh();

		$this->assertEquals($book->title, $book->title_search_helper);

		$book->title = uniqid();
		$book->updateTitleAuthorsHelper();
		$book->save();
		$book->refresh();

		$this->assertEquals($book->title, $book->title_search_helper);
	}

	public function testFulltextSearchScopeSpecialSymbols()
	{
		$title = Str::random(10);

		$book = factory(Book::class)
			->states('with_create_user')
			->create(['title' => 'ё' . $title]);

		$this->assertEquals(1, Book::query()->titleAuthorsFulltextSearch('ё' . $title)->count());
		$this->assertEquals(1, Book::query()->titleAuthorsFulltextSearch('е' . $title)->count());
	}

	public function testPrivateBookPolicy()
	{
		$book = factory(Book::class)
			->states('private', 'with_create_user')
			->create();

		$user = $book->create_user;
		$user->group->book_keyword_vote = true;
		$user->push();

		$section = factory(Section::class)->create(['book_id' => $book->id]);
		$attachment = factory(Attachment::class)->create(['book_id' => $book->id]);
		$file = factory(BookFile::class)->states('txt')->create(['book_id' => $book->id]);

		$book->refresh();

		$this->assertTrue($user->can('update', $book));
		$this->assertTrue($user->can('delete', $book));
		$this->assertFalse($user->can('group', $book));
		$this->assertFalse($user->can('ungroup', $book));
		$this->assertFalse($user->can('make_main_in_group', $book));
		$this->assertTrue($user->can('change_access', $book));
		$this->assertTrue($user->can('commentOn', $book));
		$this->assertTrue($user->can('add_similar_book', $book));
		$this->assertTrue($user->can('view_section_list', $book));
		$this->assertTrue($user->can('view_group_books', $book));
		$this->assertFalse($user->can('watch_activity_logs', $book));
		$this->assertFalse($user->can('display_technical_information', $book));
		$this->assertTrue($user->can('refresh_counters', $book));
		$this->assertFalse($user->can('close_comments', $book));
		$this->assertTrue($user->can('read', $book));
		$this->assertTrue($user->can('view_download_files', $book));
		$this->assertTrue($user->can('download', $book));
		$this->assertTrue($user->can('read_or_download', $book));
		$this->assertTrue($user->can('attachAward', $book));
		$this->assertFalse($user->can('view_deleted', $book));

		$this->assertTrue($user->can('create_section', $book));
		$this->assertTrue($user->can('update', $section));
		$this->assertTrue($user->can('delete', $section));
		$this->assertTrue($user->can('save_sections_position', $book));
		$this->assertTrue($user->can('move_sections_to_notes', $book));

		$this->assertTrue($user->can('create_attachment', $book));
		$this->assertTrue($user->can('delete', $attachment));
		$this->assertTrue($user->can('setAsCover', $attachment));

		$this->assertTrue($user->can('addFiles', $book));
		$this->assertTrue($user->can('update', $file));
		$this->assertTrue($user->can('delete', $file));
		$this->assertTrue($user->can('set_source_and_make_pages', $file));

		$book_keyword = factory(BookKeyword::class)
			->create(['book_id' => $book->id]);

		$this->assertTrue($user->can('addKeywords', $book));
		$this->assertTrue($user->can('delete', $book_keyword));
		$this->assertTrue($user->can('vote', $book_keyword));
	}

	public function testRefreshPrivateChaptersCount()
	{
		$book = factory(Book::class)->create();

		$section = factory(Section::class)->states('private')
			->create(['book_id' => $book->id]);

		$section2 = factory(Section::class)->states('private')
			->create(['book_id' => $book->id]);

		$book->refreshPrivateChaptersCount();
		$book->save();

		$this->assertEquals(2, $book->private_chapters_count);
	}

	public function testAutoCreateAverageRatingForPeriodInDatabase()
	{
		$user = factory(User::class)->create();

		$book = new Book();
		$book->title = Str::random(8);
		$book->create_user()->associate($user);
		$book->save();

		$this->assertDatabaseHas('books_average_rating_for_period', [
			'book_id' => $book->id,
			'day_vote_average' => 0
		]);
	}
}
