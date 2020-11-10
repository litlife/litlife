<?php

namespace Tests\Feature\Book;

use App\Author;
use App\Book;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Tests\TestCase;

class BookAccessTest extends TestCase
{
	public function testChangeReason()
	{
		$admin = User::factory()->admin()->create();

		$book = Book::factory()->create();
		$book->downloadAccessEnable();
		$book->readAccessEnable();
		$book->save();

		$reason = Str::random(12);

		$this->actingAs($admin)
			->followingRedirects()
			->post(route('books.access.save', ['book' => $book]), [
				'download_access' => false,
				'read_access' => false,
				'secret_hide_reason' => $reason
			])
			->assertOk()
			->assertSeeText(__('book.access_settings_have_been_successfully_changed'))
			->assertSeeText($reason);

		$book->refresh();
		$this->assertFalse($book->isDownloadAccess());
		$this->assertFalse($book->isReadAccess());
		$this->assertEquals($reason, $book->secret_hide_reason);
	}

	public function testDontSaveIfNothingChange()
	{
		$admin = User::factory()->admin()->create();

		$book = Book::factory()->create();
		$book->downloadAccessDisable();
		$book->readAccessEnable();
		$book->save();

		$this->actingAs($admin)
			->followingRedirects()
			->post(route('books.access.save', ['book' => $book]), [
				'download_access' => false,
				'read_access' => true,
			])
			->assertOk()
			->assertDontSeeText(__('book.access_settings_have_been_successfully_changed'));

		$book->refresh();
		$this->assertFalse($book->isDownloadAccess());
		$this->assertTrue($book->isReadAccess());
	}

	public function testCanEnableDownloadAccessIfFilesCountEnough()
	{
		$admin = User::factory()->admin()->create();

		$book = Book::factory()->create();
		$book->downloadAccessDisable();
		$book->files_count = 1;
		$book->save();

		$this->actingAs($admin)
			->followingRedirects()
			->post(route('books.access.save', ['book' => $book]), [
				'download_access' => true
			])
			->assertOk()
			->assertDontSeeText(__('book.to_access_the_download_at_least_one_file_must_be_attached_to_the_book'));

		$book->refresh();
		$this->assertTrue($book->isDownloadAccess());
	}

	public function testCantEnableDownloadAccessIfFilesCountNotEnough()
	{
		$admin = User::factory()->admin()->create();

		$book = Book::factory()->create();
		$book->downloadAccessDisable();
		$book->files_count = 0;
		$book->save();

		$this->actingAs($admin)
			->followingRedirects()
			->post(route('books.access.save', ['book' => $book]), [
				'download_access' => true
			])
			->assertOk()
			->assertSeeText(__('book.to_access_the_download_at_least_one_file_must_be_attached_to_the_book'))
			->assertSeeText(__('Click to find out how to attach a file'));

		$book->refresh();
		$this->assertFalse($book->isDownloadAccess());
	}

	public function testCantEnableReadAccessIfNotEnoughCharactersCount()
	{
		$admin = User::factory()->admin()->create();

		$book = Book::factory()->create();
		$book->readAccessDisable();
		$book->characters_count = 90;
		$book->save();

		$this->actingAs($admin)
			->followingRedirects()
			->post(route('books.access.save', ['book' => $book]), [
				'read_access' => true
			])
			->assertOk()
			->assertSeeText(__('book.a_book_must_have_at_least100_characters_in_order_to_be_able_to_read'));

		$book->refresh();
		$this->assertFalse($book->isReadAccess());
	}

	public function testCanEnableReadAccessIfEnoughCharactersCount()
	{
		$admin = User::factory()->admin()->create();

		$book = Book::factory()->create();
		$book->readAccessDisable();
		$book->characters_count = 110;
		$book->save();

		$this->actingAs($admin)
			->followingRedirects()
			->post(route('books.access.save', ['book' => $book]), [
				'read_access' => true
			])
			->assertOk()
			->assertDontSeeText(__('book.a_book_must_have_at_least100_characters_in_order_to_be_able_to_read'));

		$book->refresh();
		$this->assertTrue($book->isReadAccess());
	}

	public function testCanDownloadIfUserCanCheckFiles()
	{
		$admin = User::factory()->create();
		$admin->group->book_file_add_check = true;
		$admin->push();

		$book = Book::factory()->sent_for_review()->create();

		$this->assertTrue($admin->can('download', $book));
		$this->assertTrue($admin->can('view_download_files', $book));
	}

	public function testCanDownloadIfUserHasAccessToClosedBooks()
	{
		$admin = User::factory()->create();
		$admin->group->access_to_closed_books = true;
		$admin->push();

		$book = Book::factory()->accepted()->create();
		$book->downloadAccessDisable();
		$book->save();

		$this->assertTrue($admin->can('download', $book));
		$this->assertTrue($admin->can('view_download_files', $book));
	}

	public function testCantDownloadIfAccessToDownloadClosed()
	{
		$admin = User::factory()->create();

		$book = Book::factory()->accepted()->create();
		$book->downloadAccessDisable();
		$book->save();

		$this->assertFalse($admin->can('download', $book));
		$this->assertFalse($admin->can('view_download_files', $book));
	}

	public function testCantReadAccessDisableIfBookPurchased()
	{
		$user = User::factory()->create();
		$user->group->book_secret_hide_set = true;
		$user->push();

		$book = Book::factory()->with_writer()->create();
		$book->bought_times_count = 0;
		$book->push();

		$this->assertTrue($user->can('change_access', $book));

		$book = Book::factory()->with_writer()->with_read_and_download_access()->create();
		$book->bought_times_count = 1;
		$book->push();

		$this->assertFalse($user->can('change_access', $book));

		$book->readAccessDisable();
		$book->push();

		$this->assertTrue($user->can('change_access', $book));
	}

	public function testRemovedFromSaleIfRemoveAccessWarning()
	{
		$author = Author::factory()->with_author_manager_can_sell()->with_book()->create();

		$user = $author->managers->first()->user;
		$book = $author->books->first();

		$book->create_user()->associate($user);
		$book->price = 40;
		$book->is_si = true;
		$book->is_lp = false;
		$book->push();

		$this->assertTrue($user->can('sell', $book));

		$this->actingAs($user)
			->get(route('books.access.edit', ['book' => $book]))
			->assertOk()
			->assertSeeText(__('book.book_will_be_removed_from_sale_if_you_remove_access_to_reading_and_downloading'));

		$book->readAccessDisable();
		$book->downloadAccessDisable();
		$book->save();

		$this->actingAs($user)
			->get(route('books.access.edit', ['book' => $book]))
			->assertOk()
			->assertDontSeeText(__('book.book_will_be_removed_from_sale_if_you_remove_access_to_reading_and_downloading'));
	}

	public function testYouNeedEnableReadOrDownloadAccessWarning()
	{
		$author = Author::factory()->with_author_manager_can_sell()->with_book()->create();

		$user = $author->managers->first()->user;
		$book = $author->books->first();

		$book->create_user()->associate($user);
		$book->readAccessDisable();
		$book->downloadAccessDisable();
		$book->price = 40;
		$book->push();

		$this->actingAs($user)
			->get(route('books.sales.edit', ['book' => $book]))
			->assertOk()
			->assertSeeText(__('book.for_the_book_to_start_being_sold_you_need_to_allow_access_to_reading_or_downloading'));

		$book->readAccessDisable();
		$book->downloadAccessDisable();
		$book->price = 0;
		$book->save();

		$this->actingAs($user)
			->get(route('books.sales.edit', ['book' => $book]))
			->assertOk()
			->assertDontSeeText(__('book.for_the_book_to_start_being_sold_you_need_to_allow_access_to_reading_or_downloading'));
	}

	public function testCanDeleteIfBookRejectedAndCooldownIsOver()
	{
		config(['litlife.book_removed_from_sale_cooldown_in_days' => 5]);

		$author = Author::factory()->with_author_manager()->with_book_for_sale_purchased()->create();

		$manager = $author->managers->first();
		$book = $author->books->first();
		$user = $manager->user;
		$book->statusReject();
		$book->save();

		$this->assertTrue($book->isRejected());
		$this->assertFalse($user->can('delete', $book));
		$this->assertFalse($user->can('change_access', $book));

		Carbon::setTestNow(now()->addDays(config('litlife.book_removed_from_sale_cooldown_in_days'))->subHour());

		$this->assertFalse($user->can('delete', $book));
		$this->assertFalse($user->can('change_access', $book));

		Carbon::setTestNow(now()->addDays(config('litlife.book_removed_from_sale_cooldown_in_days'))->addHour());

		$this->assertTrue($user->can('delete', $book));
		$this->assertTrue($user->can('change_access', $book));
	}

	public function testChangeAccessSaveHttp()
	{
		config(['activitylog.enabled' => true]);

		$admin = User::factory()->administrator()->create();

		$book = Book::factory()->with_writer()->with_read_and_download_access()->create();

		$this->assertTrue($book->isReadAccess());
		$this->assertTrue($book->isDownloadAccess());

		$reason = $this->faker->realText(50);

		$this->actingAs($admin)
			->followingRedirects()
			->post(route('books.access.save', $book), [
				'read_access' => false,
				'download_access' => false,
				'secret_hide_reason' => $reason
			])
			->assertOk()
			->assertSeeText(__('book.access_settings_have_been_successfully_changed'));

		$book->refresh();
		$this->assertFalse($book->isReadAccess());
		$this->assertFalse($book->isDownloadAccess());

		$this->assertEquals(1, $book->activities()->count());
		$activity = $book->activities()->first();
		$this->assertEquals('change_access', $activity->description);
		$this->assertEquals($admin->id, $activity->causer_id);
		$this->assertEquals('user', $activity->causer_type);

		$this->assertEquals(false, $activity->getExtraProperty('read_access'));
		$this->assertEquals(false, $activity->getExtraProperty('download_access'));
		$this->assertEquals($reason, $activity->getExtraProperty('secret_hide_reason'));
	}

	public function testAuthorCantSellIfNoReadOrDownloadAccessPolicy()
	{
		$author = Author::factory()->with_author_manager_can_sell()->with_book()->create();

		$user = $author->managers->first()->user;
		$book = $author->books->first();
		$book->readAccessDisable();
		$book->downloadAccessDisable();

		$book->create_user()->associate($user);
		$book->push();

		$this->assertFalse($user->can('sell', $book));
	}

	public function testAuthorCanSellIfReadAccessAndDownloadAccessDisablePolicy()
	{
		$author = Author::factory()->with_author_manager_can_sell()->with_book()->create();

		$user = $author->managers->first()->user;
		$book = $author->books->first();
		$book->readAccessDisable();
		$book->downloadAccessEnable();

		$book->create_user()->associate($user);
		$book->is_si = true;
		$book->is_lp = false;
		$book->push();

		$this->assertTrue($user->can('sell', $book));
	}

	public function testIfReadDownloadAccessDisableThenRemoveFromSaleIfBookOnSale()
	{
		$author = Author::factory()->with_author_manager_can_sell()->with_book()->create();

		$user = $author->managers->first()->user;
		$book = $author->books->first();

		$book->create_user()->associate($user);
		$book->readAccessEnable();
		$book->downloadAccessEnable();
		$book->price = 40;
		$book->price_updated_at = now();
		$book->push();

		$this->assertNotNull($book->price_updated_at);

		$this->actingAs($user)
			->post(route('books.access.save', $book), [
				'read_access' => false,
				'download_access' => false
			])
			->assertSessionHasNoErrors()
			->assertRedirect();

		$book->refresh();
		$this->assertFalse($book->isReadAccess());
		$this->assertFalse($book->isDownloadAccess());
		$this->assertEquals(0, $book->price);
		$this->assertNull($book->price_updated_at);

		$priceChangeLog = $book->priceChangeLogs()->orderBy('id', 'desc')->first();

		$this->assertNotNull($priceChangeLog);
		$this->assertEquals(null, $priceChangeLog->price);
	}

	public function testIfCloseAccessThenRemoveFromSaleIfBookOnSale()
	{
		$author = Author::factory()->with_author_manager_can_sell()->with_book()->create();

		$user = $author->managers->first()->user;
		$book = $author->books->first();

		$book->create_user()->associate($user);
		$book->readAccessEnable();
		$book->downloadAccessEnable();
		$book->price = 40;
		$book->price_updated_at = now();
		$book->push();

		$this->assertNotNull($book->price_updated_at);

		$this->actingAs($user)
			->get(route('books.close_access', $book))
			->assertSessionHasNoErrors()
			->assertRedirect(route('books.access.edit', $book));

		$book->refresh();
		$this->assertTrue($book->isReadAccess());
		$this->assertTrue($book->isDownloadAccess());
		$this->assertEquals(40, $book->price);
		$this->assertNotNull($book->price_updated_at);
	}

	public function testIfReadDownloadAccessDisableAndIfBookPurchasedThenShowWarningRemoveFromSale()
	{
		$author = Author::factory()->with_author_manager_can_sell()->with_book_for_sale_purchased()->create();

		$user = $author->managers->first()->user;
		$book = $author->books->first();

		$book->create_user()->associate($user);
		$book->readAccessEnable();
		$book->downloadAccessEnable();
		$book->price = 40;
		$book->price_updated_at = now();
		$book->push();

		$this->assertNotNull($book->price_updated_at);

		$response = $this->actingAs($user)
			->get(route('books.close_access', $book))
			->assertRedirect(route('books.access.edit', $book));

		$book->refresh();
		$this->assertTrue($book->isReadAccess());
		$this->assertTrue($book->isDownloadAccess());
		$this->assertEquals(40, $book->price);
		$this->assertNotNull($book->price_updated_at);
	}

	public function testCloseAccessAndIfBookPurchasedThenShowWarningRemoveFromSale()
	{
		$author = Author::factory()->with_author_manager_can_sell()->with_book_for_sale_purchased()->create();

		$user = $author->managers->first()->user;
		$book = $author->books->first();

		$book->create_user()->associate($user);
		$book->readAccessEnable();
		$book->downloadAccessEnable();
		$book->price = 40;
		$book->price_updated_at = now();
		$book->push();

		$this->assertNotNull($book->price_updated_at);

		$response = $this->actingAs($user)
			->post(route('books.access.save', $book), [
				'read_access' => false,
				'download_access' => false
			])
			->assertRedirect(route('books.sales.edit', $book));

		$this->assertSessionHasErrors(__('book.please_remove_the_book_from_sale_to_completely_block_access_to_the_book'));

		$book->refresh();
		$this->assertTrue($book->isReadAccess());
		$this->assertTrue($book->isDownloadAccess());
		$this->assertEquals(40, $book->price);
		$this->assertNotNull($book->price_updated_at);
	}

	public function testRedirectToSalesEditIfBookOnSale()
	{
		$author = Author::factory()->with_author_manager_can_sell()->with_book()->create();

		$user = $author->managers->first()->user;
		$book = $author->books->first();

		$book->create_user()->associate($user);
		$book->readAccessEnable();
		$book->downloadAccessEnable();
		$book->price = 40;
		$book->push();

		$this->assertNull($book->price_updated_at);

		$this->actingAs($user)
			->get(route('books.close_access', $book))
			->assertRedirect(route('books.access.edit', $book));
	}

	public function testSeeOpenAccessAlertIfUserIsVerifiedAuthorOfBook()
	{
		$author = Author::factory()->with_author_manager()->with_book()->create();

		$user = $author->managers->first()->user;
		$book = $author->books->first();
		$book->readAccessDisable();
		$book->downloadAccessDisable();
		$book->save();

		$this->actingAs($user)
			->get(route('books.show', $book))
			->assertOk()
			->assertSeeText(__('book.access_to_reading_and_downloading_book_files_is_currently_closed'))
			->assertSeeText(__('book.click_here_to_open_access'));
	}
}
