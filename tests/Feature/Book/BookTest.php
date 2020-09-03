<?php

namespace Tests\Feature\Book;

use App\Attachment;
use App\Author;
use App\Book;
use App\BookFile;
use App\BookKeyword;
use App\Console\Commands\BookFillDBFromSource;
use App\Genre;
use App\Http\Requests\StoreBook;
use App\Jobs\Book\BookUpdateCharactersCountJob;
use App\Jobs\Book\UpdateBookFilesCount;
use App\Jobs\User\UpdateUserCreatedBooksCount;
use App\Library\AddFb2File;
use App\Manager;
use App\Section;
use App\Sequence;
use App\User;
use App\UserPurchase;
use Carbon\Carbon;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Litlife\Fb2\Fb2;
use Tests\TestCase;

class BookTest extends TestCase
{
	/**
	 * A basic test example.
	 *
	 * @return void
	 */
	public function setUp(): void
	{
		parent::setUp();
	}

	public function testIndexHttp()
	{
		$this->get(route('books'))
			->assertOk();
	}

	public function testCreateNewSectionsOverExisted()
	{
		Storage::fake(config('filesystems.default'));

		$book = factory(Book::class)->create();

		$command = new BookFillDBFromSource();
		$command->setExtension('epub');
		$command->setBook($book);
		$command->setStream(fopen(__DIR__ . '/Books/test.epub', 'r'));
		$command->addFromFile();

		$book->refresh();
		$section = $book->sections()->first();

		$this->assertEquals(3, $book->sections()->count());

		$response = $this->get(route('books.sections.show', ['book' => $book, 'section' => $section->inner_id]))
			->assertOk();

		$command = new BookFillDBFromSource();
		$command->setExtension('epub');
		$command->setBook($book);
		$command->setStream(fopen(__DIR__ . '/Books/test.epub', 'r'));
		$command->addFromFile();

		$book->refresh();
		$section = $book->sections()->first();

		$this->assertEquals(3, $book->sections()->count());

		$response = $this->get(route('books.sections.show', ['book' => $book, 'section' => $section->inner_id]))
			->assertOk();
	}

	public function testCreateAttachmentPolicy()
	{
		$admin = factory(User::class)->create();

		$book = factory(Book::class)->create();
		$book->statusAccepted();
		$book->push();

		$this->assertFalse($admin->can('create_attachment', $book));

		$admin->group->edit_self_book = true;
		$admin->group->edit_other_user_book = true;
		$admin->push();

		$this->assertTrue($admin->can('create_attachment', $book));
	}

	public function testGuestSeeOnReview()
	{
		$book = factory(Book::class)
			->states('sent_for_review')
			->create();

		$response = $this->get(route('books.show', $book))
			->assertOk()
			->assertSeeText($book->title)
			->assertSeeText(__('book.on_check'))
			->assertDontSeeText(__('book.you_will_receive_a_notification_when_the_book_is_published'))
			->assertDontSeeText(__('book.added_for_check'));
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

	public function testSequenceSearch()
	{
		Storage::fake(config('filesystems.default'));

		$fb2 = new Fb2();
		$fb2->setFile(__DIR__ . '/Books/test.fb2');

		foreach ($fb2->description()->getFirstChild('title-info')->childs('sequences') as $sequence) {

			$this->assertEquals('Title', $sequence->getNode()->getAttribute('name'));
			$this->assertEquals('1', $sequence->getNode()->getAttribute('number'));
		}

		Sequence::where('name', 'ilike', 'Title')
			->delete();

		$book = factory(Book::class)->create([
			'create_user_id' => 50000,
			'is_si' => false,
			'is_lp' => false,
			'age' => 0
		]);

		$sequence = factory(Sequence::class)
			->create(['name' => 'New title']);

		$addFb2File = new AddFb2File();
		$addFb2File->setBook($book);
		$addFb2File->setFile(__DIR__ . '/Books/test.fb2');
		$addFb2File->init();

		$book->refresh();

		$this->assertEquals('Title', $book->sequences->first()->name);
	}

	public function testViewFilesOnReviewIfBookOnReview()
	{
		foreach (BookFile::sentOnReview()->get() as $file)
			$file->delete();

		$book = factory(Book::class)->create();
		$book->statusSentForReview();
		$book->save();
		$book->refresh();

		$book_file = factory(BookFile::class)->states('txt')->create(['book_id' => $book->id]);
		$book_file->statusSentForReview();
		$book_file->save();
		UpdateBookFilesCount::dispatch($book);
		$book->refresh();

		$this->get(route('books.show', $book))
			->assertOk()
			->assertDontSeeText($book_file->extension);

		$admin = factory(User::class)->states('with_user_group')->create();
		$admin->group->book_file_add_check = true;
		$admin->push();

		$user = factory(User::class)->create();

		$this->actingAs($admin)
			->get(route('books.show', $book))
			->assertOk()
			->assertSeeText($book_file->extension);

		$this->actingAs($user)
			->get(route('books.show', $book))
			->assertOk()
			->assertDontSeeText($book_file->extension);
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

	public function testEditTitle()
	{
		$user = factory(User::class)
			->create();
		$user->group->edit_self_book = true;
		$user->group->edit_other_user_book = true;
		$user->push();

		$book = factory(Book::class)
			->states('with_writer', 'with_genre')
			->create();

		$array = $book->toArray();
		$array = [
			'title' => 'V.',
			'genres' => $book->genres()->pluck('id')->toArray(),
			'writers' => $book->writers()->any()->pluck('id')->toArray(),
			'ti_lb' => 'RU', 'ti_olb' => 'RU', 'ready_status' => 'complete'
		];

		$this->actingAs($user)
			->followingRedirects()
			->get(route('books.edit', $book))
			->assertOk();

		$response = $this->patch(route('books.update', $book), $array)
			->assertRedirect();

		$response->assertSessionHasNoErrors();

		$book->refresh();

		$this->assertEquals('V.', $book->title);

		$this->assertEquals($book->title_search_helper,
			mb_strtolower($book->title));
	}

	public function testCreatePolicy()
	{
		$book = factory(Book::class)
			->create();

		$user = factory(User::class)
			->states('with_user_group')
			->create();

		$this->assertFalse($user->can('create', $book));

		$user->group->add_book = true;
		$user->push();

		$this->assertTrue($user->can('create', $book));
	}

	public function testReadDownloadIfBookForSalePolicy()
	{
		$book = factory(Book::class)
			->states('with_section')
			->create(['price' => 100]);

		$user = factory(User::class)
			->create();

		$this->assertFalse($user->can('read', $book));
		$this->assertFalse($user->can('download', $book));
		$this->assertFalse($user->can('read_or_download', $book));

		$purchase = factory(UserPurchase::class)
			->create([
				'buyer_user_id' => $user->id,
				'purchasable_id' => $book->id,
				'purchasable_type' => 'book'
			]);

		$book->refresh();

		$this->assertEquals($purchase->purchasable->id, $book->id);

		$this->assertTrue($user->can('read', $book));
		$this->assertTrue($user->can('download', $book));
		$this->assertTrue($user->can('read_or_download', $book));
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

	public function testCantBuyIfAuthorPolicy()
	{
		$author = factory(Author::class)
			->states('with_author_manager', 'with_book_for_sale')
			->create();

		$user = $author->managers->first()->user;
		$book = $author->books->first();

		$this->assertFalse($user->can('buy', $book));
	}

	public function testCantBuyIfAlreadyBuyPolicy()
	{
		$purchase = factory(UserPurchase::class)
			->states('book')
			->create();

		$buyer = $purchase->buyer;
		$book = $purchase->purchasable;

		$this->assertFalse($buyer->can('buy', $book));
	}

	public function testCanReadOrDownloadIfAuthorPolicy()
	{
		$author = factory(Author::class)
			->states('with_author_manager', 'with_book_for_sale')
			->create();

		$user = $author->managers->first()->user;
		$book = $author->books->first();

		$this->assertTrue($user->can('read', $book));
		$this->assertTrue($user->can('download', $book));
		$this->assertTrue($user->can('read_or_download', $book));
	}

	public function testSellPolicy()
	{
		$author = factory(Author::class)
			->states('with_author_manager_can_sell', 'with_book')
			->create();

		$user = $author->managers->first()->user;
		$book = $author->books->first();
		$book->create_user()->associate($user);
		$book->is_si = true;
		$book->is_lp = false;
		$book->push();

		$this->assertTrue($user->can('sell', $book));

		$author = factory(Author::class)
			->states('with_author_manager', 'with_book')
			->create();

		$user = $author->managers->first()->user;
		$book = $author->books->first();
		$book->create_user()->associate($user);
		$book->is_si = true;
		$book->is_lp = false;
		$book->push();

		$this->assertFalse($user->can('sell', $book));
	}

	public function testAuthorCantSellIfNotCreatorOfTheBookPolicy()
	{
		$author = factory(Author::class)
			->states('with_author_manager_can_sell', 'with_book')
			->create();

		$user = $author->managers->first()->user;
		$book = $author->books->first();

		$this->assertFalse($user->can('sell', $book));
	}

	public function testAuthorCanSellIfUserCreatorOfTheBookPolicy()
	{
		$author = factory(Author::class)
			->states('with_author_manager_can_sell', 'with_book')
			->create();

		$user = $author->managers->first()->user;
		$book = $author->books->first();
		$book->create_user()->associate($user);
		$book->is_si = true;
		$book->is_lp = false;
		$book->push();

		$this->assertTrue($user->can('sell', $book));
	}

	public function testAuthorCanTSellIfBookDeletedPolicy()
	{
		$author = factory(Author::class)
			->states('with_author_manager_can_sell', 'with_book')
			->create();

		$user = $author->managers->first()->user;
		$book = $author->books->first();
		$book->create_user()->associate($user);
		$book->push();
		$book->delete();
		$book->refresh();

		$this->assertFalse($user->can('sell', $book));
	}

	public function testViewBookIfUserBuyThisBook()
	{
		$book = factory(Book::class)
			->create();

		$book->delete();

		$reader = factory(User::class)
			->create();

		$purchase = factory(UserPurchase::class)
			->create([
				'buyer_user_id' => $reader->id,
				'purchasable_type' => 'book',
				'purchasable_id' => $book->id,
			]);

		$this->actingAs($reader)
			->get(route('books.show', $book))
			->assertOk()
			->assertDontSeeText($book->title)
			->assertSeeText(__('Book was deleted'));
	}


	public function testUpdateHttpAnotherAuthorAppear()
	{
		$author = factory(Author::class)
			->states('with_author_manager', 'with_book')
			->create();

		$user = $author->managers->first()->user;

		$book = $author->books()->first();
		$book->create_user_id = $user->id;
		$book->save();

		$this->assertTrue($book->isAccepted());
		$this->assertTrue($user->can('update', $book));

		$author = factory(Author::class)
			->create(['last_name' => 'test']);

		$post = [
			'title' => $book->title,
			'genres' => [$book->genres()->first()->id],
			'writers' => [$book->writers()->first()->id, $author->id],
			'ti_lb' => 'RU',
			'ti_olb' => 'RU',
			'ready_status' => 'complete'
		];

		$this->actingAs($user)
			->patch(route('books.update', $book), $post)
			->assertSessionHasNoErrors()
			->assertRedirect();

		$book->refresh();

		$this->assertTrue($book->isSentForReview());

		$book->statusAccepted();
		$book->save();
		$book->refresh();

		$post['ti_lb'] = 'EN';

		$this->actingAs($user)
			->patch(route('books.update', $book), $post)
			->assertSessionHasNoErrors()
			->assertRedirect();

		$book->refresh();

		$this->assertTrue($book->isAccepted());
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

	public function testDisplayAdsForPurchasedBooksPolicy()
	{
		$user = factory(User::class)
			->create();

		$book = factory(Book::class)
			->create();

		$this->assertTrue($user->can('display_ads', $book));

		$this->assertTrue((new User)->can('display_ads', $book));

		$purchase = factory(UserPurchase::class)
			->create([
				'buyer_user_id' => $user->id,
				'purchasable_type' => 'book',
				'purchasable_id' => $book->id
			]);

		$book->refresh();

		$this->assertFalse($user->can('display_ads', $book));
	}

	public function testDisplayAdsForAuthorOfBookPolicy()
	{
		$author = factory(Author::class)
			->states('with_author_manager', 'with_book')
			->create();

		$user = $author->managers->first()->user;
		$book = $author->books->first();

		$this->assertFalse($user->can('display_ads', $book));

		$user = factory(User::class)
			->create();

		$this->assertTrue($user->can('display_ads', $book));
	}

	public function testDontDisplayAdsIfBookOnSale()
	{
		$book = factory(Book::class)
			->states('on_sale', 'with_section')
			->create();

		$user = factory(User::class)->create();

		$this->assertFalse($user->can('display_ads', $book));
		$this->assertFalse(Gate::forUser(new User)->allows('display_ads', $book));
		$this->assertFalse(Gate::allows('display_ads', $book));

		$chapter = $book->sections()->chapter()->first();

		$page = $chapter->pages()->first();

		$this->assertFalse($user->can('display_ads', $page));
		$this->assertFalse(Gate::forUser(new User)->allows('display_ads', $page));
		$this->assertFalse(Gate::allows('display_ads', $page));
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

	public function testReturnToPrivateIfBookPurchased()
	{
		$user = factory(User::class)->create();
		$user->group->check_books = true;
		$user->push();

		$book = factory(Book::class)->create();
		$book->bought_times_count = 0;
		$book->push();

		$this->assertTrue($user->can('addToPrivate', $book));

		$book = factory(Book::class)->create();
		$book->bought_times_count = 1;
		$book->push();

		$this->assertFalse($user->can('addToPrivate', $book));
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

	public function testCantRemoveCoverIfBookForSale()
	{
		$author = factory(Author::class)
			->states('with_book_for_sale', 'with_author_manager_can_sell')
			->create();

		$manager = $author->managers->first();
		$book = $author->books->first();
		$user = $manager->user;

		$cover = factory(Attachment::class)
			->states('cover')
			->create(['book_id' => $book->id]);

		$this->assertNotNull($book->fresh()->cover);
		$this->assertTrue($book->isForSale());

		$this->assertFalse($user->can('remove_cover', $book));
		$this->assertFalse($user->can('delete', $cover));

		$this->actingAs($user)
			->get(route('books.remove_cover', ['book' => $book]))
			->assertForbidden();
	}

	public function testCantCutAnnotationIfBookForSale()
	{
		config(['litlife.min_annotation_characters_count_for_sale' => 10]);

		$author = factory(Author::class)
			->states('with_book_for_sale', 'with_author_manager_can_sell')
			->create();

		$manager = $author->managers->first();
		$book = $author->books->first();
		$user = $manager->user;

		$book->is_si = true;
		$book->create_user()->associate($user);
		$book->save();

		$annotation = factory(Section::class)
			->states('annotation')
			->create(['book_id' => $book->id]);

		$this->assertNotNull($book->fresh()->annotation);
		$this->assertTrue($book->isForSale());

		$input = [
			'title' => $book->title,
			'genres' => [$book->genres()->first()->id],
			'writers' => $book->writers()->any()->pluck('id')->toArray(),
			'ti_lb' => 'RU',
			'ti_olb' => 'RU',
			'ready_status' => 'complete',
			'annotation' => '123'
		];

		$response = $this->actingAs($user)
			->patch(route('books.update', ['book' => $book]), $input)
			->assertRedirect()
			->assertSessionHasErrors(['annotation' => __('book.annotation_must_contain_at_least_characters_for_sale', [
				'characters_count' => config('litlife.min_annotation_characters_count_for_sale')
			])]);

		$this->assertEquals('123', session('_old_input')['annotation']);

		$input['annotation'] = '12345678910';

		$response = $this->actingAs($user)
			->patch(route('books.update', ['book' => $book]), $input)
			->assertRedirect()
			->assertSessionHasNoErrors();
	}

	public function testRemoveFromSalePolicy()
	{
		$author = factory(Author::class)
			->states('with_author_manager_can_sell', 'with_book')
			->create();

		$manager = $author->managers->first();
		$book = $author->books->first();
		$user = $manager->user;

		$this->assertFalse($user->can('remove_from_sale', $book));

		$author = factory(Author::class)
			->states('with_author_manager', 'with_book_for_sale')
			->create();

		$manager = $author->managers->first();
		$book = $author->books->first();
		$user = $manager->user;

		$this->assertFalse($user->can('remove_from_sale', $book));
	}

	public function testCantSaleBookIfRemovedFromSale()
	{
		$author = factory(Author::class)
			->states('with_author_manager_can_sell', 'with_book')
			->create();

		$manager = $author->managers->first();
		$book = $author->books->first();
		$user = $manager->user;
		$book->statusReject();
		$book->price = null;
		$book->save();

		$this->assertFalse($user->can('sell', $book));
		$this->assertFalse($user->can('change_sell_settings', $book));

		$user = factory(User::class)->create();

		$this->assertFalse($user->can('buy_button', $book));
		$this->assertFalse($user->can('buy', $book));
	}

	public function testSeeTextRemoveFromSaleHttp()
	{
		$book = factory(Book::class)
			->states('removed_from_sale')
			->create();

		$user = factory(User::class)
			->create();

		$this->actingAs($user)
			->get(route('books.show', ['book' => $book]))
			->assertOk()
			->assertSeeText(__('book.removed_from_sale'));
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

	public function testOpenCommentsHttp()
	{
		config(['activitylog.enabled' => true]);

		$admin = factory(User::class)->states('administrator')->create();

		$book = factory(Book::class)->create(['comments_closed' => true]);

		$this->assertTrue($book->comments_closed);

		$this->actingAs($admin)
			->followingRedirects()
			->get(route('books.open_comments', $book))
			->assertOk()
			->assertSeeText(__('book.comments_opened'));

		$book->refresh();
		$this->assertFalse($book->comments_closed);

		$this->assertEquals(1, $book->activities()->count());
		$activity = $book->activities()->first();
		$this->assertEquals('comments_open', $activity->description);
		$this->assertEquals($admin->id, $activity->causer_id);
		$this->assertEquals('user', $activity->causer_type);
	}

	public function testCloseCommentsHttp()
	{
		config(['activitylog.enabled' => true]);

		$admin = factory(User::class)->states('administrator')->create();

		$book = factory(Book::class)->create(['comments_closed' => false]);

		$this->assertFalse($book->comments_closed);

		$this->actingAs($admin)
			->followingRedirects()
			->get(route('books.close_comments', $book))
			->assertOk()
			->assertSeeText(__('book.comments_closed'));

		$book->refresh();
		$this->assertTrue($book->comments_closed);

		$this->assertEquals(1, $book->activities()->count());
		$activity = $book->activities()->first();
		$this->assertEquals('comments_close', $activity->description);
		$this->assertEquals($admin->id, $activity->causer_id);
		$this->assertEquals('user', $activity->causer_type);
	}


	public function testSeePrivateAuthorIfBookSentOnReview()
	{
		$book = factory(Book::class)
			->states('sent_for_review')
			->create();

		$author = factory(Author::class)
			->states('private')
			->create();

		$book->authors()->sync([$author->id]);

		$user = $author->create_user;

		$this->actingAs($user)
			->get(route('books.show', $book))
			->assertOk()
			->assertSeeText($author->name);

		$other_user = factory(User::class)->create();

		$this->actingAs($other_user)
			->get(route('books.show', $book))
			->assertOk()
			->assertDontSeeText($author->name);
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

	public function testEditFieldOfPublicDomainPolicy()
	{
		$user = factory(User::class)
			->create();

		$book = factory(Book::class)->create();

		$this->assertFalse($user->can('editFieldOfPublicDomain', $book));

		$user->group->edit_field_of_public_domain = true;
		$user->push();

		$this->assertTrue($user->can('editFieldOfPublicDomain', $book));
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

	public function testShowSentForReviewOk()
	{
		$user = factory(User::class)->create();

		$book = factory(Book::class)
			->states('sent_for_review')
			->create();
		$book->status_changed_user_id = $user->id;
		$book->save();

		$this->assertTrue($book->isSentForReview());

		$this->get(route('books.show', $book))
			->assertOk();
	}

	public function testShowPrivateBook()
	{
		$book = factory(Book::class)
			->states('private', 'with_create_user')
			->create();

		$this->get(route('books.show', $book))
			->assertForbidden()
			->assertSeeText(__('book.access_denied'));
	}

	public function testShowSentForReviewBook()
	{
		$book = factory(Book::class)
			->states('sent_for_review')
			->create();

		$this->get(route('books.show', $book))
			->assertOk()
			->assertSeeText(__('book.on_check'));
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
