<?php

namespace Tests\Feature\Book\Sale;

use App\Attachment;
use App\Author;
use App\Book;
use App\Notifications\BookRemovedFromSaleNotification;
use App\PriceChangeLog;
use App\Section;
use App\User;
use App\UserPurchase;
use Carbon\Carbon;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Tests\TestCase;

class BookSaleTest extends TestCase
{
	public function testCanReadOrDownloadIfAuthorPolicy()
	{
		$author = factory(Author::class)
			->states('with_author_manager', 'with_book_for_sale')
			->create();

		$user = $author->managers->first()->user;
		$book = $author->books->first();

		$this->assertTrue($user->can('read', $book));
		$this->assertTrue($user->can('view_download_files', $book));
		$this->assertTrue($user->can('download', $book));
		$this->assertTrue($user->can('read_or_download', $book));
	}

	public function testCantReadDownloadIfBookForSaleNotPurchased()
	{
		$book = factory(Book::class)
			->states('with_writer', 'with_section', 'with_read_and_download_access')
			->create(['price' => 100]);

		$user = factory(User::class)
			->create();

		$this->assertFalse($user->can('read', $book));
		$this->assertTrue($user->can('view_download_files', $book));
		$this->assertFalse($user->can('download', $book));
		$this->assertFalse($user->can('read_or_download', $book));
	}

	public function testCanReadOrDownloadIfBookForSalePurchased()
	{
		$book = factory(Book::class)
			->states('with_section')
			->create(['price' => 100]);

		$user = factory(User::class)
			->create();

		$purchase = factory(UserPurchase::class)
			->create([
				'buyer_user_id' => $user->id,
				'purchasable_id' => $book->id,
				'purchasable_type' => 'book'
			]);

		$book->refresh();

		$this->assertEquals($purchase->purchasable->id, $book->id);

		$this->assertTrue($user->can('read', $book));
		$this->assertTrue($user->can('view_download_files', $book));
		$this->assertTrue($user->can('download', $book));
		$this->assertTrue($user->can('read_or_download', $book));
	}

	public function testChangePriceCooldown()
	{
		config(['litlife.minimum_characters_count_before_book_can_be_sold' => 0]);
		config(['litlife.book_price_update_cooldown' => 7]);

		$now = now();

		$author = factory(Author::class)
			->states('with_author_manager_can_sell', 'with_book_cover_annotation')
			->create();

		$user = $author->managers->first()->user;
		$book = $author->books->first();

		$book->create_user()->associate($user);
		$book->is_si = true;
		$book->is_lp = false;
		$book->sections_count = 3;
		$book->push();

		$this->assertTrue($user->can('sell', $book));
		$this->assertNull($book->price_updated_at);

		$this->actingAs($user)
			->post(route('books.sales.save', $book),
				[
					'free_sections_count' => 1
				])
			->assertRedirect()
			->assertSessionHasErrors();

		$book->refresh();

		$this->assertNull($book->price_updated_at);

		//

		$this->actingAs($user)
			->get(route('books.sales.edit', $book))
			->assertOk();

		$this->actingAs($user)
			->post(route('books.sales.save', $book),
				[
					'price' => 100,
					'free_sections_count' => 1
				])
			->assertSessionHasNoErrors()
			->assertRedirect();

		$book->refresh();

		$this->assertEquals(100, $book->price);
		$this->assertEquals(1, $book->free_sections_count);
		$this->assertNotNull($book->price_updated_at);

		//

		Carbon::setTestNow($now->copy()->addMinute());

		$response = $this->actingAs($user)
			->post(route('books.sales.save', $book),
				[
					'price' => 90,
					'free_sections_count' => 2
				])
			->assertRedirect();

		$response->assertSessionHasErrors(['price' =>
			trans_choice('book.book_price_cant_changed_within_period_days', config('litlife.book_price_update_cooldown'), ['days_count' => config('litlife.book_price_update_cooldown')])]);

		$book->refresh();

		$this->assertEquals(100, $book->price);
		$this->assertEquals(1, $book->free_sections_count);
		$this->assertNotNull($book->price_updated_at);

		//

		Carbon::setTestNow($now->copy()->addDays(config('litlife.book_price_update_cooldown'))->addHour());

		$this->actingAs($user)
			->post(route('books.sales.save', $book),
				[
					'price' => 110
				])
			->assertRedirect()
			->assertSessionHasNoErrors();

		$book->refresh();

		$this->assertEquals(110, $book->price);
		$this->assertNull($book->free_sections_count);
		$this->assertNotNull($book->price_updated_at);
	}

	public function testMinimumCharactersCountBeforeBookCanBeSold()
	{
		config(['litlife.minimum_characters_count_before_book_can_be_sold' => 500]);
		config(['litlife.book_price_update_cooldown' => 0]);

		$author = factory(Author::class)
			->states('with_author_manager_can_sell', 'with_book_cover_annotation')
			->create();

		$user = $author->managers->first()->user;
		$book = $author->books->first();

		$book->create_user()->associate($user);
		$book->characters_count = 400;
		$book->is_si = true;
		$book->is_lp = false;
		$book->push();

		$this->assertTrue($user->can('sell', $book));

		$this->actingAs($user)
			->post(route('books.sales.save', $book),
				[
					'price' => 100
				])
			->assertRedirect(route('books.sales.edit', $book))
			->assertSessionHasErrors(['error' => __('book.minimum_characters_count_before_book_can_be_sold',
				['characters_count' => config('litlife.minimum_characters_count_before_book_can_be_sold')])]);

		$book->characters_count = 600;
		$book->push();

		$this->actingAs($user)
			->post(route('books.sales.save', $book),
				[
					'price' => 100
				])
			->assertRedirect(route('books.sales.edit', $book))
			->assertSessionHasNoErrors();

		$book->characters_count = 400;
		$book->price = 100;
		$book->push();

		$this->actingAs($user)
			->post(route('books.sales.save', $book),
				[
					'price' => 0
				])
			->assertRedirect()
			->assertSessionHasNoErrors();

		$this->assertEquals('0', $book->fresh()->price);
	}

	public function testForSaleMustHaveCover()
	{
		$author = factory(Author::class)
			->states('with_book', 'with_author_manager_can_sell')
			->create();

		$manager = $author->managers->first();
		$book = $author->books->first();
		$book->price = 0;
		$book->is_si = true;
		$book->is_lp = false;
		$book->save();

		$user = $manager->user;

		$this->assertNull($book->cover);

		$response = $this->actingAs($user)
			->get(route('books.sales.edit', $book))
			->assertOk()
			->assertSeeText(__('book.book_must_have_a_cover_for_sale'));

		$response = $this->actingAs($user)
			->post(route('books.sales.save', $book),
				[
					'price' => '50'
				])
			->assertRedirect();

		$this->assertSessionHasErrors(__('book.book_must_have_a_cover_for_sale'));

		$this->assertEquals(0, $book->fresh()->price);

		$cover = factory(Attachment::class)
			->states('cover')
			->create(['book_id' => $book->id]);

		$book->refresh();

		$this->assertNotNull($book->cover);

		$response = $this->actingAs($user)
			->get(route('books.sales.edit', $book))
			->assertOk();

		$response = $this->actingAs($user)
			->get(route('books.sales.edit', $book))
			->assertOk()
			->assertDontSeeText(__('book.book_must_have_a_cover_for_sale'));
	}

	public function testForSaleMustHaveAnnotation()
	{
		config(['litlife.min_annotation_characters_count_for_sale' => 10]);

		$author = factory(Author::class)
			->states('with_book_cover_annotation', 'with_author_manager_can_sell')
			->create();

		$manager = $author->managers->first();
		$book = $author->books->first();
		$book->price = 0;
		$book->is_si = true;
		$book->is_lp = false;
		$book->annotation->delete();
		$book->push();

		$user = $manager->user;

		$response = $this->actingAs($user)
			->get(route('books.sales.edit', $book))
			->assertOk()
			->assertSeeText(__('book.annotation_must_contain_at_least_characters_for_sale', ['characters_count' => config('litlife.min_annotation_characters_count_for_sale')]));

		$response = $this->actingAs($user)
			->post(route('books.sales.save', $book),
				[
					'price' => '50'
				])
			->assertRedirect();

		$this->assertSessionHasErrors(__('book.annotation_must_contain_at_least_characters_for_sale', ['characters_count' => config('litlife.min_annotation_characters_count_for_sale')]));

		$book->refresh();

		$this->assertEquals(0, $book->price);
		$this->assertNull($book->annotation);

		$annotation = factory(Section::class)
			->states('annotation')
			->create([
				'book_id' => $book->id
			]);
		$annotation->content = '1234567';
		$annotation->save();

		$this->assertNotNull($book->fresh()->annotation);
		$this->assertLessThan(10, $book->fresh()->annotation->character_count);

		$response = $this->actingAs($user)
			->get(route('books.sales.edit', $book))
			->assertOk()
			->assertSeeText(__('book.annotation_must_contain_at_least_characters_for_sale', ['characters_count' => config('litlife.min_annotation_characters_count_for_sale')]));

		$annotation->content = '12345678910';
		$annotation->save();

		$response = $this->actingAs($user)
			->get(route('books.sales.edit', $book))
			->assertOk()
			->assertDontSeeText(__('book.annotation_must_contain_at_least_characters_for_sale', ['characters_count' => config('litlife.min_annotation_characters_count_for_sale')]));
	}

	public function testCantSaleNotCompleteBookIfOtherNotCompleteBookNotExists()
	{
		$author = factory(Author::class)
			->states('with_author_manager_can_sell')
			->create();

		$book = factory(Book::class)
			->states('with_cover',
				'with_annotation',
				'not_complete_but_still_writing', 'with_section')
			->create();

		$manager = $author->managers->first();
		$user = $manager->user;
		$book->is_si = true;
		$book->is_lp = false;
		$book->create_user()->associate($user);
		$book->save();
		$book->writers()->detach();

		$author->books()->sync([$book->id]);

		$response = $this->actingAs($user)
			->get(route('books.sales.edit', $book))
			->assertOk()
			->assertSeeText(__('book.for_the_sale_of_an_unfinished_book_you_must_have_at_least_one_completed_book_added'));

		$response = $this->actingAs($user)
			->post(route('books.sales.save', $book),
				[
					'price' => '50'
				])
			->assertSessionHasErrors(['price' => __('book.for_the_sale_of_an_unfinished_book_you_must_have_at_least_one_completed_book_added')])
			->assertRedirect();

		$this->assertEquals(0, $book->fresh()->price);
	}

	public function testCanSaleNotCompleteBookIfOtherCompleteBookExists()
	{
		$author = factory(Author::class)
			->states('with_author_manager_can_sell')
			->create();

		$manager = $author->managers->first();
		$user = $manager->user;

		$book2 = factory(Book::class)
			->states('complete')
			->create();

		$book2->price = 100;
		$book2->is_si = true;
		$book2->is_lp = false;
		$book2->create_user()->associate($user);
		$book2->save();

		$book = factory(Book::class)
			->states('with_cover',
				'with_annotation',
				'not_complete_but_still_writing', 'with_section')
			->create();
		$book->characters_count = config('litlife.minimum_characters_count_before_book_can_be_sold') + 1000;
		$book->is_si = true;
		$book->is_lp = false;
		$book->create_user()->associate($user);
		$book->save();
		$book->writers()->detach();

		$author->books()->sync([$book->id, $book2->id]);

		$response = $this->actingAs($user)
			->get(route('books.sales.edit', $book))
			->assertOk()
			->assertViewHas('seller', $user)
			->assertViewHas('author', $author)
			->assertViewHas('seller_manager', $manager)
			->assertDontSeeText(__('book.for_the_sale_of_an_unfinished_book_you_must_have_at_least_one_completed_book_added'));

		$response = $this->actingAs($user)
			->post(route('books.sales.save', $book),
				[
					'price' => '50'
				])
			->assertSessionHasNoErrors()
			->assertRedirect();

		$this->assertEquals(50, $book->fresh()->price);
	}

	public function testRemoveFromSale()
	{
		$author = factory(Author::class)
			->states('with_author_manager_can_sell', 'with_book_for_sale')
			->create();

		$manager = $author->managers->first();
		$book = $author->books->first();
		$user = $manager->user;

		$this->assertTrue($book->isForSale());
		$this->assertTrue($user->can('remove_from_sale', $book));

		$this->actingAs($user)
			->get(route('books.remove_from_sale', ['book' => $book]))
			->assertRedirect(route('books.sales.edit', ['book' => $book]))
			->assertSessionHas(['success' => __('book.removed_from_sale')]);

		$book->refresh();

		$this->assertFalse($book->isForSale());
		$this->assertTrue($book->isRejected());
	}

	public function testRemoveFromSaleNotifications()
	{
		Notification::fake();

		$author = factory(Author::class)
			->states('with_author_manager_can_sell', 'with_book_for_sale_purchased')
			->create();

		$manager = $author->managers->first();
		$book = $author->books->first();
		$user = $manager->user;
		$buyer = $book->boughtUsers->first();

		$this->assertNotNull($buyer);

		$this->actingAs($user)
			->get(route('books.remove_from_sale', ['book' => $book]))
			->assertRedirect(route('books.sales.edit', ['book' => $book]))
			->assertSessionHas(['success' => __('book.removed_from_sale')]);

		Notification::assertSentTo(
			$buyer,
			BookRemovedFromSaleNotification::class,
			function ($notification, $channels) use ($book, $buyer) {
				$this->assertContains('mail', $channels);
				$this->assertContains('database', $channels);

				$mail = $notification->toMail($buyer);

				$this->assertEquals(__('notification.book_removed_from_sale.subject', ['book_title' => $book->title]), $mail->subject);

				$this->assertEquals(__('notification.book_removed_from_sale.line', [
					'book_title' => $book->title,
					'writers_names' => implode(', ', $book->writers->pluck('name')->toArray()),
					'days' => config('litlife.book_removed_from_sale_cooldown_in_days')
				]), $mail->introLines[0]);

				$this->assertEquals(__('notification.book_removed_from_sale.action'), $mail->actionText);

				$this->assertEquals(route('books.show', ['book' => $book]), $mail->actionUrl);

				$array = $notification->toArray($book);

				$this->assertEquals(__('notification.book_removed_from_sale.subject', ['book_title' => $book->title]), $array['title']);

				$this->assertEquals(__('notification.book_removed_from_sale.line', [
					'book_title' => $book->title,
					'writers_names' => implode(', ', $book->writers->pluck('name')->toArray()),
					'days' => config('litlife.book_removed_from_sale_cooldown_in_days')
				]), $array['description']);

				$this->assertEquals(route('books.show', ['book' => $book]), $array['url']);

				return $notification->book->id == $book->id;
			}
		);
	}

	public function testSaleBookCreatedByOtherUserWarning()
	{
		$author = factory(Author::class)
			->states('with_author_manager_can_sell', 'with_book')
			->create();

		$manager = $author->managers->first();
		$book = $author->books->first();
		$user = $manager->user;
		$book->create_user(factory(User::class)->create());
		$book->save();

		$this->assertFalse($user->can('sell', $book));
		$this->assertTrue($user->can('change_sell_settings', $book));

		$this->actingAs($user)
			->get(route('books.sales.edit', ['book' => $book]))
			->assertOk()
			->assertSeeText(__('book.book_added_by_another_user'));
	}

	public function testCantSellBookIfOtherWriterExists()
	{
		$author = factory(Author::class)
			->states('with_author_manager_can_sell', 'with_book')
			->create();

		$manager = $author->managers->first();
		$book = $author->books->first();
		$book->is_si = true;
		$book->is_lp = false;
		$book->save();

		$user = $manager->user;

		$author2 = factory(Author::class)
			->create();

		$book->writers()->attach([$author2->id]);

		$this->assertEquals(2, $book->writers()->count());

		$this->assertFalse($user->can('sell', $book));

		$this->actingAs($user)
			->get(route('books.sales.edit', ['book' => $book]))
			->assertOk()
			->assertSeeText(__('book.we_dont_have_the_opportunity_to_sell_books_with_more_than_one_writer'));

		$this->actingAs($user)
			->post(route('books.sales.save', ['book' => $book]), [
				'price' => 100,
				'free_sections_count' => 0
			])
			->assertRedirect();

		$this->assertSessionHasErrors(__('book.we_dont_have_the_opportunity_to_sell_books_with_more_than_one_writer'));
	}

	public function testToSellBookYouNeedRequestHttp()
	{
		$author = factory(Author::class)
			->states('with_author_manager', 'with_book_for_sale')
			->create();

		$manager = $author->managers->first();
		$book = $author->books->first();
		$user = $manager->user;
		$book->create_user()->associate($user);
		$book->save();

		$this->assertTrue($user->can('author', $book));
		$this->assertFalse($user->can('sell', $book));

		$this->actingAs($user)
			->get(route('books.sales.edit', ['book' => $book]))
			->assertOk()
			->assertViewHas('author', false)
			->assertSeeText(__('book.to_sell_books_you_need_to_request'))
			->assertSeeText(__('book.sent_request'));

		$manager->can_sale = true;
		$manager->save();
		$book->refresh();

		$this->assertTrue($user->can('author', $book));
		$this->assertTrue($user->can('sell', $book));

		$this->actingAs($user)
			->get(route('books.sales.edit', ['book' => $book]))
			->assertOk()
			->assertSeeText(__('book.price'))
			->assertSeeText(__('book.free_sections_count'));
	}

	public function testYouCantSaleBookFragment()
	{
		$author = factory(Author::class)
			->states('with_author_manager_can_sell', 'with_book')
			->create();

		$manager = $author->managers->first();
		$book = $author->books->first();
		$user = $manager->user;
		$book->create_user()->associate($user);
		$book->ready_status = 'complete_but_publish_only_part';
		$book->is_si = true;
		$book->is_lp = false;
		$book->save();

		$this->actingAs($user)
			->get(route('books.sales.edit', ['book' => $book]))
			->assertOk()
			->assertSeeText(__('book.only_finished_or_in_the_process_of_writing_books_are_allowed_to_be_sold'));

		$this->actingAs($user)
			->post(route('books.sales.save', ['book' => $book]), [
				'price' => 100,
				'free_sections_count' => 0
			])
			->assertRedirect();

		$this->assertSessionHasErrors(__('book.only_finished_or_in_the_process_of_writing_books_are_allowed_to_be_sold'));
		$this->assertEquals(0, $book->price);
	}

	public function testYouCantSaleBookIfItNotWillBeComplete()
	{
		$author = factory(Author::class)
			->states('with_author_manager_can_sell', 'with_book')
			->create();

		$manager = $author->managers->first();
		$book = $author->books->first();
		$user = $manager->user;
		$book->create_user()->associate($user);
		$book->ready_status = 'not_complete_and_not_will_be';
		$book->save();

		$this->actingAs($user)
			->get(route('books.sales.edit', ['book' => $book]))
			->assertOk()
			->assertSeeText(__('book.only_finished_or_in_the_process_of_writing_books_are_allowed_to_be_sold'));

		$this->actingAs($user)
			->post(route('books.sales.save', ['book' => $book]))
			->assertRedirect();

		$this->assertEquals(0, $book->price);
	}

	public function testCantBuyOrSellIfNotWillBeFinishedAndIfPublishOnlyFragment()
	{
		$author = factory(Author::class)
			->states('with_author_manager_can_sell', 'with_book_for_sale')
			->create();

		$manager = $author->managers->first();
		$book = $author->books->first();
		$user = $manager->user;
		$book->create_user()->associate($user);
		$book->ready_status = 'complete_but_publish_only_part';
		$book->save();

		$buyer = factory(User::class)->create();

		$this->assertFalse($user->can('sell', $book));
		$this->assertFalse($buyer->can('buy', $book));
		$this->assertFalse($buyer->can('buy_button', $book));
	}

	public function testCantChangeAuthorIfBookOnSale()
	{
		$author = factory(Author::class)
			->states('with_author_manager_can_sell', 'with_book_for_sale')
			->create();

		$manager = $author->managers->first();
		$book = $author->books->first();
		$user = $manager->user;
		$book->create_user()->associate($user);
		$book->save();

		$new_author = factory(Author::class)->create();

		$array = [
			'title' => $book->title,
			'genres' => $book->genres()->pluck('id')->toArray(),
			'writers' => [$new_author->id],
			'ti_lb' => 'RU',
			'ti_olb' => 'RU',
			'ready_status' => 'complete',
			'is_public' => true,
			'year_public' => rand(2000, 2010),
			'annotation' => $this->faker->realText(1000)
		];

		$this->actingAs($user)
			->patch(route('books.update', $book), $array)
			->assertSessionHasErrors(['writers' => __('book.you_cannot_change_the_data_in_the_writers_field_if_the_book_is_on_sale')])
			->assertRedirect();

		$book->refresh();

		$this->assertEquals($author->id, pos($book->writers()->pluck('id')->toArray()));
	}

	public function testCanChangeAuthorIfBookNotOnSale()
	{
		$author = factory(Author::class)
			->states('with_author_manager_can_sell', 'with_book')
			->create();

		$manager = $author->managers->first();
		$book = $author->books->first();
		$user = $manager->user;
		$book->create_user()->associate($user);
		$book->save();

		$new_author = factory(Author::class)->create();

		$array = [
			'title' => $book->title,
			'genres' => $book->genres()->pluck('id')->toArray(),
			'writers' => [$new_author->id],
			'ti_lb' => 'RU',
			'ti_olb' => 'RU',
			'ready_status' => 'complete',
			'is_public' => true,
			'year_public' => rand(2000, 2010),
			'annotation' => $this->faker->realText(1000)
		];

		$this->actingAs($user)
			->patch(route('books.update', $book), $array)
			->assertSessionHasNoErrors()
			->assertRedirect();

		$book->refresh();

		$this->assertEquals($new_author->id, pos($book->writers()->pluck('id')->toArray()));
	}

	public function testCantSellNotSiBooks()
	{
		$buyer = factory(User::class)->create();

		config(['litlife.minimum_characters_count_before_book_can_be_sold' => 0]);
		config(['litlife.book_price_update_cooldown' => 7]);

		$now = now();

		$author = factory(Author::class)
			->states('with_author_manager_can_sell', 'with_book_cover_annotation')
			->create();

		$user = $author->managers->first()->user;
		$book = $author->books->first();

		$book->create_user()->associate($user);
		$book->is_si = true;
		$book->is_lp = false;
		$book->price = 100;
		$book->push();

		$this->assertTrue($user->can('sell', $book));
		$this->assertTrue($buyer->can('buy', $book));
		$this->assertTrue($buyer->can('buy_button', $book));

		$this->actingAs($user)
			->get(route('books.sales.edit', ['book' => $book]))
			->assertOk()
			->assertDontSeeText(__('book.only_si_books_are_allowed_to_be_sold'));

		$book->is_si = false;
		$book->is_lp = false;
		$book->push();

		$this->assertFalse($user->can('sell', $book));
		$this->assertFalse($buyer->can('buy', $book));
		$this->assertFalse($buyer->can('buy_button', $book));

		$this->actingAs($user)
			->get(route('books.sales.edit', ['book' => $book]))
			->assertOk()
			->assertSeeText(__('book.only_si_books_are_allowed_to_be_sold'));
	}

	public function testCantSellLpBooks()
	{
		$buyer = factory(User::class)->create();

		config(['litlife.minimum_characters_count_before_book_can_be_sold' => 0]);
		config(['litlife.book_price_update_cooldown' => 7]);

		$now = now();

		$author = factory(Author::class)
			->states('with_author_manager_can_sell', 'with_book_cover_annotation')
			->create();

		$user = $author->managers->first()->user;
		$book = $author->books->first();

		$book->create_user()->associate($user);
		$book->is_si = true;
		$book->is_lp = false;
		$book->price = 100;
		$book->push();

		$this->actingAs($user)
			->get(route('books.sales.edit', ['book' => $book]))
			->assertOk()
			->assertDontSeeText(__('book.amateur_translations_cannot_be_sold'));

		$this->assertTrue($user->can('sell', $book));
		$this->assertTrue($buyer->can('buy', $book));
		$this->assertTrue($buyer->can('buy_button', $book));

		$book->is_si = true;
		$book->is_lp = true;
		$book->push();

		$this->assertFalse($user->can('sell', $book));
		$this->assertFalse($buyer->can('buy', $book));
		$this->assertFalse($buyer->can('buy_button', $book));

		$this->actingAs($user)
			->get(route('books.sales.edit', ['book' => $book]))
			->assertOk()
			->assertSeeText(__('book.amateur_translations_cannot_be_sold'));

		$this->actingAs($user)
			->post(route('books.sales.save', ['book' => $book]), [
				'price' => 100,
				'free_sections_count' => 0
			])
			->assertRedirect();

		$this->assertSessionHasErrors(__('book.amateur_translations_cannot_be_sold'));
	}

	public function testCantChangeSIorLPIfBookOnSale()
	{
		$author = factory(Author::class)
			->states('with_author_manager_can_sell', 'with_book')
			->create();

		$manager = $author->managers->first();
		$book = $author->books->first();
		$user = $manager->user;
		$book->create_user()->associate($user);
		$book->is_si = true;
		$book->is_lp = false;
		$book->price = 100;
		$book->save();

		$array = [
			'title' => $book->title,
			'genres' => $book->genres()->pluck('id')->toArray(),
			'writers' => [$author->id],
			'ti_lb' => 'RU',
			'ti_olb' => 'RU',
			'ready_status' => 'complete',
			'is_public' => true,
			'year_public' => rand(2000, 2010),
			'annotation' => $this->faker->realText(1000),
			'is_si' => false,
			'is_lp' => true
		];

		$this->actingAs($user)
			->patch(route('books.update', $book), $array)
			->assertSessionHasNoErrors()
			->assertRedirect();

		$book->refresh();

		$this->assertTrue($book->is_si);
		$this->assertFalse($book->is_lp);
	}

	public function testSeeBuyButtonIfUserGuest()
	{
		$title = Str::random(8);

		$author = factory(Author::class)
			->states('with_author_manager_can_sell', 'with_book')
			->create();
		$book = $author->books->first();
		$book->price = 100;
		$book->is_si = true;
		$book->is_lp = false;
		$book->title = $title;
		$book->push();

		$this->assertTrue(Gate::authorize('buy_button', $book)->allowed());

		$this->get(route('books.show', $book))
			->assertOk()
			->assertSeeText(trans_choice('book.buy_a_book', $book->price, ['price' => $book->price]));

		$this->get(route('books', ['search' => $title]))
			->assertOk()
			->assertSeeText(trans_choice('book.buy_a_book', $book->price, ['price' => $book->price]));
	}

	public function testBookWillNotBeSoldIfTheNumberOfFreeChaptersIsGreaterThanOrEqualToTheNumberOfChapters()
	{
		$author = factory(Author::class)
			->states('with_author_manager_can_sell', 'with_book_for_sale')
			->create();

		$user = $author->managers->first()->user;
		$book = $author->books->first();
		$book->create_user()->associate($user);
		$book->push();

		$this->assertEquals(1, $book->sections_count);
		$this->assertEquals(0, $book->free_sections_count);
		$this->assertFalse($book->isDisplaySaleWarning());

		$this->actingAs($user)
			->get(route('books.sales.edit', ['book' => $book]))
			->assertOk()
			->assertDontSeeText(__('book.the_book_will_not_be_sold_if_the_number_of_free_chapters_is_greater_than_or_equal_to_the_number_of_chapters', ['sections_count' => $book->sections_count]));

		$book->price = 50;
		$book->sections_count = 1;
		$book->free_sections_count = 1;
		$book->save();
		$book->refresh();
		$this->assertTrue($book->isDisplaySaleWarning());

		$this->actingAs($user)
			->get(route('books.sales.edit', ['book' => $book]))
			->assertOk()
			->assertSeeText(__('book.the_book_will_not_be_sold_if_the_number_of_free_chapters_is_greater_than_or_equal_to_the_number_of_chapters', ['sections_count' => $book->sections_count]));

		$book->sections_count = 1;
		$book->free_sections_count = 2;
		$book->save();
		$book->refresh();
		$this->assertTrue($book->isDisplaySaleWarning());

		$this->actingAs($user)
			->get(route('books.sales.edit', ['book' => $book]))
			->assertOk()
			->assertSeeText(__('book.the_book_will_not_be_sold_if_the_number_of_free_chapters_is_greater_than_or_equal_to_the_number_of_chapters', ['sections_count' => $book->sections_count]));

		$this->actingAs($user)
			->post(route('books.sales.save', ['book' => $book]), [
				'price' => 100,
				'free_sections_count' => 3
			])
			->assertRedirect()
			->assertSessionHasErrors(['free_sections_count' => __('book.the_book_will_not_be_sold_if_the_number_of_free_chapters_is_greater_than_or_equal_to_the_number_of_chapters', ['sections_count' => $book->sections_count])]);

		$book->refresh();

		$this->assertEquals(50, $book->price);
		$this->assertEquals(2, $book->free_sections_count);
	}

	public function testCantSetMoreOrEqualsFreeChaptersCountThanChaptersCount()
	{
		$author = factory(Author::class)
			->states('with_author_manager_can_sell', 'with_book_for_sale')
			->create();

		$user = $author->managers->first()->user;
		$book = $author->books->first();
		$book->create_user()->associate($user);
		$book->sections_count = 10;
		$book->free_sections_count = 3;
		$book->push();

		$this->actingAs($user)
			->post(route('books.sales.save', ['book' => $book]), [
				'price' => 100,
				'free_sections_count' => 10
			])
			->assertRedirect()
			->assertSessionHasErrors(['free_sections_count' => __('book.the_book_will_not_be_sold_if_the_number_of_free_chapters_is_greater_than_or_equal_to_the_number_of_chapters', ['sections_count' => $book->sections_count])]);

		$book->refresh();

		$this->assertEquals(3, $book->free_sections_count);
	}

	public function testCanSetLowerFreeChaptersCountThaChaptersCount()
	{
		config(['litlife.minimum_characters_count_before_book_can_be_sold' => 8000]);

		$author = factory(Author::class)
			->states('with_author_manager_can_sell', 'with_book_for_sale')
			->create();

		$user = $author->managers->first()->user;
		$book = $author->books->first();
		$book->create_user()->associate($user);
		$book->sections_count = 10;
		$book->characters_count = 10000;
		$book->push();

		$this->actingAs($user)
			->post(route('books.sales.save', ['book' => $book]), [
				'price' => 100,
				'free_sections_count' => 4
			])
			->assertRedirect()
			->assertSessionHasNoErrors();

		$book->refresh();

		$this->assertEquals(4, $book->free_sections_count);
	}

	public function testCantBuyBookIfFreeChaptersCountMoreOrEqualsChaptersCount()
	{
		$author = factory(Author::class)
			->states('with_author_manager_can_sell', 'with_book_for_sale')
			->create();

		$user = $author->managers->first()->user;
		$book = $author->books->first();
		$book->create_user()->associate($user);
		$book->push();

		$buyer = factory(User::class)->create();

		$this->assertTrue($buyer->can('buy', $book));
		$this->assertTrue($buyer->can('buy_button', $book));

		$book->sections_count = 1;
		$book->free_sections_count = 1;
		$book->save();
		$book->refresh();

		$this->assertFalse($buyer->can('buy', $book));
		$this->assertFalse($buyer->can('buy_button', $book));

		$book->sections_count = 1;
		$book->free_sections_count = 4;
		$book->save();
		$book->refresh();

		$this->assertFalse($buyer->can('buy', $book));
		$this->assertFalse($buyer->can('buy_button', $book));
	}

	public function testSeeSectionIfUserHasAccessToClosedBooks()
	{
		$author = factory(Author::class)
			->states('with_author_manager_can_sell', 'with_book_for_sale')
			->create();

		$user = $author->managers->first()->user;
		$book = $author->books->first();
		$book->create_user()->associate($user);
		$book->push();

		$admin = factory(User::class)->create();
		$admin->group->access_to_closed_books = true;
		$admin->push();

		$this->assertTrue($admin->can('read', $book));

		$section = $book->sections()->chapter()->first();

		$this->assertTrue($admin->can('view', $section));

		$this->actingAs($admin)
			->get(route('books.read.online', $book))
			->assertRedirect();
	}

	public function testIsPostedFreeFragment()
	{
		$book = factory(Book::class)->create();

		$this->assertFalse($book->isPostedFreeFragment());

		$book->free_sections_count = 1;
		$book->save();

		$this->assertFalse($book->isPostedFreeFragment());

		$book->price = 10;
		$book->save();

		$this->assertTrue($book->isPostedFreeFragment());
	}

	public function testAuthorCantSetPriceIfBookAddedByAnotherUser()
	{
		$author = factory(Author::class)
			->states('with_author_manager_can_sell', 'with_book_cover_annotation')
			->create();

		$user = $author->managers->first()->user;
		$book = $author->books->first();
		$book->is_si = true;
		$book->is_lp = false;
		$book->push();

		$this->actingAs($user)
			->get(route('books.sales.edit', $book))
			->assertOk()
			->assertSeeText(__('book.book_added_by_another_user'));

		$this->actingAs($user)
			->post(route('books.sales.save', $book), [
				'price' => 100
			])
			->assertSessionHasErrors(['price' => __('book.book_added_by_another_user')])
			->assertRedirect();

		$book->refresh();

		$this->assertEquals(null, $book->price);
	}

	public function testCantSetFreeSectionsCountIfPriceNotSet()
	{
		$author = factory(Author::class)
			->states('with_author_manager_can_sell', 'with_book_cover_annotation')
			->create();

		$user = $author->managers->first()->user;
		$book = $author->books->first();
		$book->is_si = true;
		$book->is_lp = false;
		$book->create_user()->associate($user);
		$book->push();

		$section = factory(Section::class)
			->states('chapter')
			->create(['book_id' => $book->id]);

		$this->assertEquals(0, $book->price);

		$this->actingAs($user)
			->post(route('books.sales.save', $book), [
				'free_sections_count' => 1
			])
			->assertSessionHasErrors(['price' => __('book.number_of_free_chapters_must_be_zero_if_the_book_has_no_price')])
			->assertRedirect();

		$book->refresh();

		//$this->assertEquals(100, $book->price);
		$this->assertEquals(null, $book->free_sections_count);
	}

	public function testChangePriceLog()
	{
		$author = factory(Author::class)
			->states('with_author_manager_can_sell', 'with_book_for_sale')
			->create();

		$user = $author->managers->first()->user;
		$book = $author->books->first();
		$book->price = null;
		$book->create_user()->associate($user);
		$book->save();

		$this->assertEquals(0, $book->price);
		$this->assertEquals(0, $book->previous_price);
		$this->assertNull($book->price_updated_at);

		$this->actingAs($user)
			->post(route('books.sales.save', $book), [
				'price' => 100
			])
			->assertSessionHasNoErrors()
			->assertRedirect();

		$book->refresh();

		$this->assertEquals(100, $book->price);
		$this->assertEquals(0, $book->previous_price);
		$this->assertNotNull($book->price_updated_at);
		$this->assertEquals(1, $book->priceChangeLogs()->orderBy('id', 'desc')->count());

		$priceChangeLog = $book->priceChangeLogs()->first();
		$this->assertEquals(100, $priceChangeLog->price);

		Carbon::setTestNow(now()->addDays(config('litlife.book_price_update_cooldown') + 1));

		$this->actingAs($user)
			->post(route('books.sales.save', $book), [
				'price' => 200
			])
			->assertSessionHasNoErrors()
			->assertRedirect();

		$book->refresh();

		$this->assertEquals(200, $book->price);
		$this->assertEquals(100, $book->previous_price);
		$this->assertEquals(2, $book->priceChangeLogs()->count());

		$priceChangeLog = $book->priceChangeLogs()->orderBy('id', 'desc')->first();
		$this->assertEquals(200, $priceChangeLog->price);
	}

	public function testChangePriceToNull()
	{
		$author = factory(Author::class)
			->states('with_author_manager_can_sell', 'with_book_for_sale')
			->create();

		$user = $author->managers->first()->user;
		$book = $author->books->first();
		$book->create_user()->associate($user);
		$book->price = 100.5;
		$book->save();

		$this->actingAs($user)
			->post(route('books.sales.save', $book), [
				'price' => 0
			])
			->assertSessionHasNoErrors()
			->assertRedirect();

		$book->refresh();

		$this->assertEquals(0, $book->price);
		$this->assertEquals(100.5, $book->previous_price);
		$this->assertEquals(1, $book->priceChangeLogs()->orderBy('id', 'desc')->count());

		$priceChangeLog = $book->priceChangeLogs()->first();
		$this->assertEquals(0, $priceChangeLog->price);
	}

	public function testPreviousPrice()
	{
		$book = factory(Book::class)->create();
		$book->previous_price = 9.543;
		$book->save();
		$book->refresh();

		$this->assertEquals(9.54, $book->previous_price);

		$book->previous_price = null;
		$book->save();
		$book->refresh();

		$this->assertEquals(0, $book->previous_price);
	}

	public function testIsPriceHasBecomeLess()
	{
		$book = factory(Book::class)->create();

		$this->assertFalse($book->isPriceHasBecomeLess());

		$book->price = 10.5;
		$book->previous_price = 9.5;
		$book->save();
		$book->refresh();

		$this->assertFalse($book->isPriceHasBecomeLess());

		$book->price = 9.5;
		$book->previous_price = 10.5;
		$book->save();
		$book->refresh();

		$this->assertTrue($book->isPriceHasBecomeLess());

		$book->price = 10.5;
		$book->previous_price = 10.5;
		$book->save();
		$book->refresh();

		$this->assertFalse($book->isPriceHasBecomeLess());
	}

	public function testGetDiscount()
	{
		$book = factory(Book::class)->create();

		$book->price = 60;
		$book->previous_price = 100;
		$book->save();
		$book->refresh();

		$this->assertEquals(40, $book->getDiscount());

		$book->price = 100;
		$book->previous_price = 200;
		$book->save();
		$book->refresh();

		$this->assertEquals(50, $book->getDiscount());

		$book->price = 200;
		$book->previous_price = 200;
		$book->save();
		$book->refresh();

		$this->assertEquals(0, $book->getDiscount());

		$book->price = 0;
		$book->previous_price = 0;
		$book->save();
		$book->refresh();

		$this->assertFalse($book->getDiscount());

		$book->price = 101;
		$book->previous_price = 199;
		$book->save();
		$book->refresh();

		$this->assertEquals(49, $book->getDiscount());
	}

	public function testPriceChangeLog()
	{
		$log = factory(PriceChangeLog::class)
			->create();

		$log->price = 63.58;
		$log->save();
		$log->refresh();

		$this->assertEquals(63.58, $log->price);

		$log->price = null;
		$log->save();
		$log->refresh();

		$this->assertEquals(0, $log->price);
	}

	public function testSeeWarningPleaseSetSiStatusIfBookPrivate()
	{
		$buyer = factory(User::class)->create();

		config(['litlife.minimum_characters_count_before_book_can_be_sold' => 0]);
		config(['litlife.book_price_update_cooldown' => 7]);

		$author = factory(Author::class)
			->states('with_author_manager_can_sell', 'with_book_cover_annotation')
			->create();

		$user = $author->managers->first()->user;
		$book = $author->books->first();

		$book->create_user()->associate($user);
		$book->is_si = false;
		$book->is_lp = false;
		$book->statusPrivate();
		$book->push();

		$this->actingAs($user)
			->get(route('books.sales.edit', ['book' => $book]))
			->assertOk()
			->assertSeeText(__('book.please_go_to_the_book_description_editing_page_and_set_the_status_samizdat'))
			->assertDontSeeText(__('book.please_write_to_the_topic_ask_a_moderator_if_you_need_to_set_the_status_of_si'));

		$this->actingAs($user)
			->post(route('books.sales.save', ['book' => $book]), [
				'price' => '80'
			])
			->assertRedirect();

		$this->assertSessionHasErrors(__('book.please_go_to_the_book_description_editing_page_and_set_the_status_samizdat'));

		$book->refresh();

		$this->assertEquals(0, $book->price);
	}

	public function testCanSetPriceIfBookPrivate()
	{
		$buyer = factory(User::class)->create();

		config(['litlife.minimum_characters_count_before_book_can_be_sold' => 0]);
		config(['litlife.book_price_update_cooldown' => 7]);

		$author = factory(Author::class)
			->states('with_author_manager_can_sell', 'with_book_cover_annotation')
			->create();

		$user = $author->managers->first()->user;
		$book = $author->books->first();

		$book->create_user()->associate($user);
		$book->is_si = true;
		$book->is_lp = false;
		$book->statusPrivate();
		$book->push();

		$this->actingAs($user)
			->post(route('books.sales.save', ['book' => $book]), [
				'price' => '80'
			])
			->assertRedirect()
			->assertSessionHasNoErrors();

		$book->refresh();

		$this->assertEquals(80, $book->price);

		$this->assertTrue($book->isDisplaySaleWarning());

		$this->actingAs($user)
			->get(route('books.sales.edit', ['book' => $book]), [
				'price' => '80'
			])
			->assertOk()
			->assertSeeText(__('book.for_the_book_to_start_being_sold_you_must_publish_it'));
	}

	public function testPublishPrivateBookOnSale()
	{
		$user = factory(User::class)
			->states('admin')
			->create();

		$author = factory(Author::class)
			->states('with_author_manager_can_sell', 'with_book_cover_annotation')
			->create();

		$book = $author->books->first();

		$book->create_user()->associate($user);
		$book->is_si = true;
		$book->is_lp = false;
		$book->statusPrivate();
		$book->price = 80;
		$book->push();

		$this->actingAs($user)
			->followingRedirects()
			->get(route('books.publish', ['book' => $book]))
			->assertOk()
			->assertSeeText(__('book.book_has_been_published_and_is_now_on_sale'));
	}

	public function testCantSetCompleteButPublishOnlyPartOrNotCompleteAndNotWillBeReadyStatus()
	{
		config(['litlife.min_annotation_characters_count_for_sale' => 0]);

		$author = factory(Author::class)
			->states('with_author_manager_can_sell', 'with_book_for_sale')
			->create();

		$user = $author->managers->first()->user;
		$book = $author->books->first();
		$book->price = null;
		$book->create_user()->associate($user);
		$book->price = 100;
		$book->ready_status = 'complete';
		$book->save();

		$post = [
			'title' => $book->title,
			'genres' => $book->genres()->pluck('id')->toArray(),
			'writers' => $book->writers()->any()->pluck('id')->toArray(),
			'ti_lb' => 'RU',
			'ti_olb' => 'RU',
			'ready_status' => 'complete_but_publish_only_part'
		];

		$response = $this->actingAs($user)
			->patch(route('books.update', $book), $post)
			->assertRedirect();
		//var_dump(session('errors'));
		$response->assertSessionHasErrors(['ready_status' => __('book.if_the_book_is_on_sale_the_text_of_the_book_may_have_the_status_only_finished_or_not_finished_and_is_still_being_written')]);

		$book->refresh();

		$this->assertEquals('complete', $book->ready_status);

		$post['ready_status'] = 'not_complete_and_not_will_be';

		$response = $this->actingAs($user)
			->patch(route('books.update', $book), $post)
			->assertRedirect();
		//var_dump(session('errors'));
		$response->assertSessionHasErrors(['ready_status' => __('book.if_the_book_is_on_sale_the_text_of_the_book_may_have_the_status_only_finished_or_not_finished_and_is_still_being_written')]);

		$book->refresh();

		$this->assertEquals('complete', $book->ready_status);
	}

	public function testCanChangeToCompleteFromStillWriting()
	{
		config(['litlife.min_annotation_characters_count_for_sale' => 0]);

		$author = factory(Author::class)
			->states('with_author_manager_can_sell', 'with_book_for_sale')
			->create();

		$user = $author->managers->first()->user;
		$book = $author->books->first();
		$book->price = null;
		$book->create_user()->associate($user);
		$book->price = 100;
		$book->ready_status = 'complete';
		$book->save();

		$post = [
			'title' => $book->title,
			'genres' => $book->genres()->pluck('id')->toArray(),
			'writers' => $book->writers()->any()->pluck('id')->toArray(),
			'ti_lb' => 'RU',
			'ti_olb' => 'RU',
			'ready_status' => 'not_complete_but_still_writing'
		];

		$response = $this->actingAs($user)
			->patch(route('books.update', $book), $post)
			->assertRedirect()
			->assertSessionHasNoErrors();

		$book->refresh();

		$this->assertEquals('not_complete_but_still_writing', $book->ready_status);

		$post['ready_status'] = 'complete';

		$response = $this->actingAs($user)
			->patch(route('books.update', $book), $post)
			->assertRedirect()
			->assertSessionHasNoErrors();

		$book->refresh();

		$this->assertEquals('complete', $book->ready_status);
	}

	public function testIfBookIsOldOnlineReadFormat()
	{
		config(['litlife.min_annotation_characters_count_for_sale' => 0]);

		$author = factory(Author::class)
			->states('with_author_manager_can_sell', 'with_book_for_sale')
			->create();

		$user = $author->managers->first()->user;
		$book = $author->books->first();
		$book->online_read_new_format = false;
		$book->save();

		$this->actingAs($user)
			->get(route('books.sales.edit', ['book' => $book]))
			->assertOk()
			->assertSeeText(__('book.price'))
			->assertDontSeeText(__('book.free_sections_count'));
	}

	public function testTheBookShouldNotHaveChaptersWithLongerCharactersThanAllowedSoThatItCanBeSold()
	{
		config(['litlife.min_annotation_characters_count_for_sale' => 0]);

		$author = factory(Author::class)
			->states('with_author_manager_can_sell', 'with_book_for_sale')
			->create();

		$user = $author->managers->first()->user;
		$book = $author->books->first();
		$book->price = 0;
		$book->save();

		$section = $book->sections()->chapter()->first();
		$section->characters_count = config('litlife.max_section_characters_count') + 100;
		$section->save();

		$this->actingAs($user)
			->get(route('books.sales.edit', ['book' => $book]))
			->assertOk()
			->assertSeeText(__('book.book_should_be_divided_into_chapters_and_parts', ['max_symbols_count' => config('litlife.max_section_characters_count')]));

		$this->actingAs($user)
			->post(route('books.sales.save', ['book' => $book]), [
				'price' => 100,
				'free_sections_count' => 0
			])
			->assertRedirect();

		$this->assertSessionHasErrors(__('book.book_should_be_divided_into_chapters_and_parts', ['max_symbols_count' => config('litlife.max_section_characters_count')]));
		$this->assertEquals(0, $book->price);
	}

	public function testCantSellABookIfTheSellerIsntListedInTheWritersField()
	{
		config(['litlife.minimum_characters_count_before_book_can_be_sold' => 0]);
		config(['litlife.book_price_update_cooldown' => 7]);

		$author = factory(Author::class)
			->states('with_author_manager_can_sell', 'with_book_cover_annotation')
			->create();

		$user = $author->managers->first()->user;
		$book = $author->books->first();

		$book->create_user()->associate($user);
		$book->is_si = true;
		$book->is_lp = false;
		$book->statusAccepted();
		$book->push();

		$otherAuthor = factory(Author::class)->create();

		$book->writers()->sync([$otherAuthor->id]);
		$book->translators()->sync([$author->id]);
		$book->push();

		$this->assertFalse($user->can('change_sell_settings', $book));

		$this->actingAs($user)
			->get(route('books.sales.edit', ['book' => $book]))
			->assertOk()
			->assertViewHas('seller', false)
			->assertViewHas('author', false)
			->assertSeeText(__('book.you_must_specify_your_author_page_in_the_writers_field'));

		$this->actingAs($user)
			->post(route('books.sales.save', ['book' => $book]), [
				'price' => '80'
			])
			->assertForbidden()
			->assertSeeText(__('book.you_must_specify_your_author_page_in_the_writers_field'), false);

		$book->refresh();

		$this->assertEquals(0, $book->price);
	}
}
