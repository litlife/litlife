<?php

namespace Tests\Feature\Author;

use App\Author;
use App\AuthorSaleRequest;
use App\Notifications\AuthorSaleRequestAcceptedNotification;
use App\Notifications\AuthorSaleRequestRejectedNotification;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Tests\TestCase;

class AuthorSaleRequestTest extends TestCase
{
	public function setUp(): void
	{
		parent::setUp();

		config(['litlife.the_total_number_of_characters_of_the_authors_books_in_order_to_be_allowed_to_send_a_request_for_permission_to_sell_books' => 0]);

		AuthorSaleRequest::truncate();
		AuthorSaleRequest::flushCachedOnModerationCount();
	}

	public function testRequestFormHttp()
	{
		$admin = factory(User::class)->create();
		$admin->group->author_editor_request = true;
		$admin->push();

		$author = factory(Author::class)
			->states('with_author_manager')
			->create();

		$manager = $author->managers()->first();

		$response = $this->actingAs($manager->user)
			->get(route('authors.sales.request', ['author' => $author->id]))
			->assertOk();
	}

	public function testRequestStoreHttp()
	{
		$admin = factory(User::class)->create();
		$admin->group->author_editor_request = true;
		$admin->push();

		$author = factory(Author::class)
			->states('with_author_manager', 'with_complete_book')
			->create();

		$manager = $author->managers()->first();
		$book = $author->any_books()->first();
		$book->create_user()->associate($manager->user);
		$book->push();

		$text = $this->faker->realText(500) . ' ' . Str::random(11);

		$this->actingAs($manager->user)
			->get(route('authors.sales.request', ['author' => $author->id]))
			->assertOk()
			->assertViewHas('completeBooksCount', 1)
			->assertViewHas('isEnoughBooksTextCharacters', true)
			->assertViewHas('authorHasBooksAddedByAuthUser', true)
			->assertDontSeeText(__('author_sale_request.to_send_a_request_the_author_must_have_at_least_one_finished_book'))
			->assertDontSeeText(__('author_sale_request.your_author_page_must_have_at_least_one_book_added_by_you'))
			->assertDontSeeText(__('author_sale_request.to_submit_a_request_your_added_books_must_have_at_least_two_characters_of_text_in_total',
				['characters_count' => config('litlife.the_total_number_of_characters_of_the_authors_books_in_order_to_be_allowed_to_send_a_request_for_permission_to_sell_books')]));

		$response = $this->actingAs($manager->user)
			->post(route('authors.sales.store', ['author' => $author->id]), [
				'text' => $text,
				'rules_accepted' => true
			])
			->assertSessionHasNoErrors();

		$sales_request = $author->sales_request()
			->first();

		$response->assertRedirect(route('authors.sales_requests.show', ['request' => $sales_request]));

		$this->assertEquals($author->id, $sales_request->author_id);
		$this->assertEquals($manager->id, $sales_request->manager_id);
		$this->assertEquals($manager->user_id, $sales_request->create_user_id);
		$this->assertEquals($text, $sales_request->text);
		$this->assertTrue($sales_request->isSentForReview());

		$response = $this->actingAs($manager->user)
			->get(route('authors.sales.request', ['author' => $author->id]))
			->assertRedirect(route('authors.sales_requests.show', ['request' => $sales_request]));

		$response = $this->actingAs($manager->user)
			->followingRedirects()
			->get(route('authors.sales.request', ['author' => $author->id]))
			->assertOk()
			->assertSeeText(__('author_sale_request.wait_for_review'))
			->assertSeeText($text);

		$this->assertEquals(1, AuthorSaleRequest::getCachedOnModerationCount());
	}

	public function testUserCanSaleRequestPolicy()
	{
		$author = factory(Author::class)
			->states('with_author_manager')
			->create();

		$manager = $author->managers()->first();
		$user = $manager->user;

		//$this->assertTrue($user->can('view_sales_request', $author));
		$this->assertTrue($user->can('sales_request', $author));
	}

	public function testUserCantSaleRequestIfManagerIsNotAcceptedPolicy()
	{
		$author = factory(Author::class)
			->states('with_author_manager')
			->create();

		$manager = $author->managers()->first();
		$manager->statusSentForReview();
		$manager->save();
		$author->refresh();

		$user = $manager->user;

		//$this->assertFalse($user->can('view_sales_request', $author));
		$this->assertFalse($user->can('sales_request', $author));

		$manager->statusReject();
		$manager->save();
		$author->refresh();

		//$this->assertFalse($user->can('view_sales_request', $author));
		$this->assertFalse($user->can('sales_request', $author));
	}

	public function testUserCantSaleRequestIfManagerIsEditorPolicy()
	{
		$author = factory(Author::class)
			->states('with_author_manager')
			->create();

		$manager = $author->managers()->first();
		$manager->character = 'editor';
		$manager->save();
		$author->refresh();

		$user = $manager->user;

		//$this->assertFalse($user->can('view_sales_request', $author));
		$this->assertFalse($user->can('sales_request', $author));
	}

	public function testUserCantRequestIfRequestAlreadyExistsPolicy()
	{
		$author = factory(Author::class)
			->states('with_author_manager')
			->create();

		$manager = $author->managers()->first();
		$user = $manager->user;

		$saleRequest = factory(AuthorSaleRequest::class)
			->create(
				[
					'author_id' => $author->id,
					'create_user_id' => $user->id
				]
			);

		$saleRequest->statusSentForReview();
		$saleRequest->save();
		$author->refresh();

		//$this->assertTrue($user->can('view_sales_request', $author));
		$this->assertFalse($user->can('sales_request', $author));

		$saleRequest->statusReviewStarts();
		$saleRequest->save();
		$author->refresh();

		//$this->assertTrue($user->can('view_sales_request', $author));
		$this->assertFalse($user->can('sales_request', $author));

		$saleRequest->statusReject();
		$saleRequest->save();
		$author->refresh();

		//$this->assertTrue($user->can('view_sales_request', $author));
		$this->assertFalse($user->can('sales_request', $author));
	}

	public function testUserCanRequestIfRequestAlreadyExistsAndAuthorCantSalePolicy()
	{
		$author = factory(Author::class)
			->states('with_author_manager')
			->create();

		$manager = $author->managers()->first();
		$user = $manager->user;

		$saleRequest = factory(AuthorSaleRequest::class)
			->create(
				[
					'author_id' => $author->id,
					'create_user_id' => $user->id
				]
			);

		$saleRequest->statusAccepted();
		$saleRequest->save();
		$manager->statusAccepted();
		$manager->can_sale = false;
		$manager->save();
		$author->refresh();

		//$this->assertTrue($user->can('view_sales_request', $author));
		$this->assertTrue($user->can('sales_request', $author));
	}

	public function testUserCantRequestIfRequestAlreadyExistsAndAuthorCanSalePolicy()
	{
		$author = factory(Author::class)
			->states('with_author_manager')
			->create();

		$manager = $author->managers()->first();
		$user = $manager->user;

		$saleRequest = factory(AuthorSaleRequest::class)
			->create(
				[
					'author_id' => $author->id,
					'create_user_id' => $user->id
				]
			);

		$saleRequest->statusAccepted();
		$saleRequest->save();
		$manager->statusAccepted();
		$manager->can_sale = true;
		$manager->save();
		$author->refresh();

		//$this->assertFalse($user->can('view_sales_request', $author));
		$this->assertFalse($user->can('sales_request', $author));
	}

	public function testCounter()
	{
		AuthorSaleRequest::truncate();

		AuthorSaleRequest::flushCachedOnModerationCount();

		$this->assertEquals(0, AuthorSaleRequest::getCachedOnModerationCount());

		$saleRequest = factory(AuthorSaleRequest::class)
			->states('accepted')
			->create();

		AuthorSaleRequest::flushCachedOnModerationCount();

		$this->assertEquals(0, AuthorSaleRequest::getCachedOnModerationCount());

		$saleRequest = factory(AuthorSaleRequest::class)
			->states('on_review')
			->create();

		AuthorSaleRequest::flushCachedOnModerationCount();

		$this->assertEquals(1, AuthorSaleRequest::getCachedOnModerationCount());
	}

	public function testRelation()
	{
		$saleRequest = factory(AuthorSaleRequest::class)
			->states('accepted')
			->create();

		$this->assertEquals($saleRequest->manager_id, $saleRequest->manager->id);
		$this->assertEquals($saleRequest->author_id, $saleRequest->author->id);
	}

	public function testAcceptHttp()
	{
		Notification::fake();
		Notification::assertNothingSent();

		$admin = factory(User::class)->create();
		$admin->group->author_sale_request_review = true;
		$admin->push();

		$saleRequest = factory(AuthorSaleRequest::class)
			->states('starts_review')
			->create();

		$this->assertEquals(1, AuthorSaleRequest::getCachedOnModerationCount());

		$this->assertTrue($admin->can('accept', $saleRequest));

		$this->actingAs($admin)
			->get(route('authors.sales_requests.accept', ['request' => $saleRequest->id]))
			->assertRedirect(route('authors.sales_requests.show', ['request' => $saleRequest->id]))
			->assertSessionHas(['success' => __('author_sale_request.you_accept_review')]);

		$saleRequest->refresh();

		$this->assertTrue($saleRequest->isAccepted());
		$this->assertTrue($saleRequest->manager->can_sale);
		$this->assertEquals($admin->id, $saleRequest->status_changed_user_id);

		$this->assertEquals(0, AuthorSaleRequest::getCachedOnModerationCount());

		Notification::assertSentTo(
			$saleRequest->create_user,
			AuthorSaleRequestAcceptedNotification::class,
			function ($notification, $channels) use ($saleRequest) {
				$this->assertContains('mail', $channels);
				$this->assertContains('database', $channels);

				$mail = $notification->toMail($saleRequest->create_user);

				$this->assertEquals(__('notification.author_sale_request_accepted.subject'), $mail->subject);
				$this->assertEquals(__('notification.author_sale_request_accepted.line', ['author_name' => $saleRequest->author->name]), $mail->introLines[0]);
				$this->assertEquals(__('notification.author_sale_request_accepted.action'), $mail->actionText);
				$this->assertEquals(route('authors.show', ['author' => $saleRequest->author]), $mail->actionUrl);

				$array = $notification->toArray($saleRequest->create_user);

				$this->assertEquals(__('notification.author_sale_request_accepted.subject'), $array['title']);
				$this->assertEquals(__('notification.author_sale_request_accepted.line', ['author_name' => $saleRequest->author->name]), $array['description']);
				$this->assertEquals(route('authors.show', ['author' => $saleRequest->author]), $array['url']);

				return $notification->author_sale_request->id == $saleRequest->id;
			}
		);
	}

	public function testRejectHttp()
	{
		Notification::fake();
		Notification::assertNothingSent();

		$admin = factory(User::class)->create();
		$admin->group->author_sale_request_review = true;
		$admin->push();

		$review_comment = $this->faker->realText(100);

		$saleRequest = factory(AuthorSaleRequest::class)
			->states('starts_review')
			->create();

		$this->assertEquals(1, AuthorSaleRequest::getCachedOnModerationCount());

		$this->actingAs($admin)
			->post(route('authors.sales_requests.reject', ['request' => $saleRequest->id]),
				['review_comment' => $review_comment])
			->assertRedirect(route('authors.sales_requests.show', ['request' => $saleRequest->id]))
			->assertSessionHas(['success' => __('author_sale_request.you_reject_review')]);

		$saleRequest->refresh();

		$this->assertTrue($saleRequest->isRejected());
		$this->assertFalse($saleRequest->manager->can_sale);
		$this->assertEquals($admin->id, $saleRequest->status_changed_user_id);

		$this->assertEquals(0, AuthorSaleRequest::getCachedOnModerationCount());

		$this->actingAs($admin)
			->get(route('authors.sales_requests.show', ['request' => $saleRequest->id]))
			->assertOk()
			->assertSeeText(__('author_sale_request.you_can_submit_a_new_application_in_days', ['days' => config('litlife.minimum_days_to_submit_a_new_request_for_author_sale')]))
			->assertSeeText($review_comment);

		Notification::assertSentTo(
			$saleRequest->create_user,
			AuthorSaleRequestRejectedNotification::class,
			function ($notification, $channels) use ($saleRequest) {
				$this->assertContains('mail', $channels);
				$this->assertContains('database', $channels);

				$mail = $notification->toMail($saleRequest->create_user);

				$this->assertEquals(__('notification.author_sale_request_rejected.subject'), $mail->subject);
				$this->assertEquals(__('notification.author_sale_request_rejected.line', ['author_name' => $saleRequest->author->name]), $mail->introLines[0]);
				$this->assertEquals(__('notification.author_sale_request_rejected.action'), $mail->actionText);
				$this->assertEquals(route('authors.sales_requests.show', ['request' => $saleRequest]), $mail->actionUrl);

				$array = $notification->toArray($saleRequest->create_user);

				$this->assertEquals(__('notification.author_sale_request_rejected.subject'), $array['title']);
				$this->assertEquals(__('notification.author_sale_request_rejected.line', ['author_name' => $saleRequest->author->name]), $array['description']);
				$this->assertEquals(route('authors.sales_requests.show', ['request' => $saleRequest]), $array['url']);

				return $notification->author_sale_request->id == $saleRequest->id;
			}
		);
	}

	public function testStartReviewHttp()
	{
		$admin = factory(User::class)->create();
		$admin->group->author_sale_request_review = true;
		$admin->push();

		$saleRequest = factory(AuthorSaleRequest::class)
			->states('on_review')
			->create();

		$this->assertEquals(1, AuthorSaleRequest::getCachedOnModerationCount());

		$this->actingAs($admin)
			->get(route('authors.sales_requests.start_review', ['request' => $saleRequest->id]))
			->assertRedirect(route('authors.sales_requests.show', ['request' => $saleRequest->id]));

		$saleRequest->refresh();

		$this->assertTrue($saleRequest->isReviewStarts());
		$this->assertFalse($saleRequest->manager->can_sale);
		$this->assertEquals($admin->id, $saleRequest->status_changed_user_id);

		$this->assertEquals(1, AuthorSaleRequest::getCachedOnModerationCount());
	}

	public function testStopReviewHttp()
	{
		$admin = factory(User::class)->create();
		$admin->group->author_sale_request_review = true;
		$admin->push();

		$saleRequest = factory(AuthorSaleRequest::class)
			->states('starts_review')
			->create();
		$saleRequest->statusReviewStarts();
		$saleRequest->status_changed_user_id = $admin->id;
		$saleRequest->save();

		$this->assertTrue($saleRequest->isReviewStarts());

		$this->assertEquals(1, AuthorSaleRequest::getCachedOnModerationCount());

		$this->actingAs($admin)
			->get(route('authors.sales_requests.stop_review', ['request' => $saleRequest->id]))
			->assertRedirect(route('authors.sales_requests.index'))
			->assertSessionHas(['success' => __('author_sale_request.you_stop_review')]);

		$saleRequest->refresh();

		$this->assertTrue($saleRequest->isSentForReview());
		$this->assertFalse($saleRequest->manager->can_sale);

		$this->assertEquals(1, AuthorSaleRequest::getCachedOnModerationCount());
	}

	public function testCantSaleRequestIfAnotherAuthorWithSaleRequestExists()
	{
		$author = factory(Author::class)
			->states('with_two_managers_and_one_can_sell')
			->create();

		$user = $author->managers->where('can_sale', false)->first()->user;

		$this->assertFalse($user->can('sales_request', $author));
	}

	public function testViewForUserThatCreateRequest()
	{
		$saleRequest = factory(AuthorSaleRequest::class)
			->states('on_review')
			->create();

		$this->assertTrue($saleRequest->create_user->can('show', $saleRequest));
	}

	public function testSendNewRequestAgain()
	{
		config(['litlife.minimum_days_to_submit_a_new_request_for_author_sale' => 6]);

		$author = factory(Author::class)
			->states('with_complete_book')
			->create();

		$saleRequest = factory(AuthorSaleRequest::class)
			->states('rejected')
			->create(['author_id' => $author->id]);

		$user = $saleRequest->manager->user;
		$manager = $saleRequest->manager;
		$book = $author->any_books()->first();
		$book->create_user()->associate($manager->user);
		$book->push();

		$this->assertNotNull($user);
		$this->assertNotNull($manager);
		$this->assertNotNull($author);
		$this->assertEquals($manager->user_id, $user->id);

		$this->assertFalse($user->can('sales_request', $saleRequest->author));

		Carbon::setTestNow(now()->addDays(config('litlife.minimum_days_to_submit_a_new_request_for_author_sale') - 1));

		$this->assertFalse($user->can('sales_request', $saleRequest->author));

		Carbon::setTestNow(now()->addDays(config('litlife.minimum_days_to_submit_a_new_request_for_author_sale') + 1));

		$this->assertTrue($user->can('sales_request', $saleRequest->author));

		$text = $this->faker->realText(100);

		$response = $this->actingAs($user)
			->post(route('authors.sales.store', ['author' => $author->id]), [
				'text' => $text,
				'rules_accepted' => true
			])
			->assertSessionHasNoErrors();

		$sales_request = $author->sales_request()
			->latest()
			->first();

		$response->assertRedirect(route('authors.sales_requests.show', ['request' => $sales_request]));

		$this->assertEquals($author->id, $sales_request->author_id);
		$this->assertEquals($manager->id, $sales_request->manager_id);
		$this->assertEquals($manager->user_id, $sales_request->create_user_id);
		$this->assertEquals($text, $sales_request->text);
		$this->assertTrue($sales_request->isSentForReview());

		$response = $this->actingAs($user)
			->get(route('authors.sales_requests.show', ['request' => $sales_request->id]))
			->assertOk()
			->assertSeeText(__('author_sale_request.wait_for_review'))
			->assertSeeText($text);

		$this->assertEquals(1, AuthorSaleRequest::getCachedOnModerationCount());

		$this->assertEquals(2, $author->sales_request()->count());
	}

	public function testSentAnotherRequestIfOnReview()
	{
		$saleRequest = factory(AuthorSaleRequest::class)
			->states('on_review')
			->create();

		$user = $saleRequest->manager->user;
		$manager = $saleRequest->manager;
		$author = $manager->manageable;

		$this->assertFalse($user->can('sales_request', $saleRequest->author));
	}

	public function testSentAnotherRequestIfStartsReview()
	{
		$saleRequest = factory(AuthorSaleRequest::class)
			->states('starts_review')
			->create();

		$user = $saleRequest->manager->user;
		$manager = $saleRequest->manager;
		$author = $manager->manageable;

		$this->assertFalse($user->can('sales_request', $saleRequest->author));
	}

	public function testCantSentRequestIfNoCompleteBookExists()
	{
		$admin = factory(User::class)->create();
		$admin->group->author_editor_request = true;
		$admin->push();

		$author = factory(Author::class)
			->states('with_author_manager')
			->create();

		$manager = $author->managers()->first();
		$text = $this->faker->realText(100);

		$response = $this->actingAs($manager->user)
			->get(route('authors.sales.request', ['author' => $author->id]))
			->assertOk()
			->assertViewHas('completeBooksCount', 0)
			->assertSeeText(__('author_sale_request.to_send_a_request_the_author_must_have_at_least_one_finished_book'));

		$response = $this->actingAs($manager->user)
			->post(route('authors.sales.store', ['author' => $author->id]), [
				'text' => $text,
				'rules_accepted' => true
			])
			->assertRedirect(route('authors.sales.request', ['author' => $author->id]));

		$this->assertSessionHasErrors(__('author_sale_request.to_send_a_request_the_author_must_have_at_least_one_finished_book'));

		$sales_request = $author->sales_request()
			->first();

		$this->assertNull($sales_request);
	}

	public function testCanSendRequestIfBookClosedHttp()
	{
		$admin = factory(User::class)->create();
		$admin->group->author_editor_request = true;
		$admin->push();

		$author = factory(Author::class)
			->states('with_author_manager', 'with_complete_book')
			->create();

		$manager = $author->managers()->first();
		$book = $author->books()->first();
		$book->readAccessDisable();
		$book->create_user()->associate($manager->user);
		$book->save();

		$text = $this->faker->realText(100);

		$response = $this->actingAs($manager->user)
			->post(route('authors.sales.store', ['author' => $author->id]), [
				'text' => $text,
				'rules_accepted' => true
			])
			->assertSessionHasNoErrors();

		$sales_request = $author->sales_request()
			->first();

		$this->assertNotNull($sales_request);
	}

	public function testCantSendRequestOtherUser()
	{
		$author = factory(Author::class)
			->states('with_author_manager', 'with_complete_book')
			->create();

		$author2 = factory(Author::class)
			->states('with_author_manager', 'with_complete_book')
			->create();

		$manager = $author2->managers->first();
		$book = $author2->books->first();
		$user = $manager->user;

		$this->assertFalse($user->can('sales_request', $author));
		$this->assertTrue($user->can('sales_request', $author2));

		$user = factory(User::class)
			->create();

		$this->assertFalse($user->can('sales_request', $author));
		$this->assertFalse($user->can('sales_request', $author2));
	}

	public function testViewSaleRequestsHttp()
	{
		$admin = factory(User::class)
			->states('administrator')
			->create();

		$sale_request = factory(AuthorSaleRequest::class)
			->create();

		$this->actingAs($admin)
			->get(route('authors.sales_requests.index'))
			->assertOk();

		$sale_request->manager->delete();

		$this->actingAs($admin)
			->get(route('authors.sales_requests.index'))
			->assertOk();
	}

	public function testCantDeleteAuthorIfAuthorCanSale()
	{
		$author = factory(Author::class)
			->states('with_author_manager_can_sell', 'with_book_for_sale')
			->create();

		$manager = $author->managers()->first();
		$book = $author->books()->first();
		$seller = $manager->user;

		$admin = factory(User::class)
			->states('admin')
			->create();

		$this->assertFalse($admin->can('delete', $author));
	}

	public function testSentNewSaleRequestIfOtherAcceptedExists()
	{
		$author = factory(Author::class)
			->states('with_author_manager', 'with_book')
			->create();

		$manager = $author->managers()->first();
		$book = $author->books()->first();
		$user = $manager->user;

		$saleRequest = factory(AuthorSaleRequest::class)
			->states('accepted')
			->create([
				'create_user_id' => $user,
				'manager_id' => $manager->id,
				'author_id' => $author->id
			]);

		$this->assertTrue($user->can('sales_request', $author));

		$this->actingAs($user)
			->get(route('authors.sales.request', $author))
			->assertOk()
			->assertDontSeeText(__('author_sale_request.accepted'))
			->assertSeeText(__('author_sale_request.text'));
	}

	public function testCantSentRequestIfNotEnoughBooksCharactersCount()
	{
		$author = factory(Author::class)
			->states('with_author_manager', 'with_book')
			->create();

		$manager = $author->managers()->first();
		$book = $author->books()->first();
		$user = $manager->user;

		config(['litlife.the_total_number_of_characters_of_the_authors_books_in_order_to_be_allowed_to_send_a_request_for_permission_to_sell_books' => 1000]);

		$book->create_user()->associate($manager->user);
		$book->characters_count = 999;
		$book->save();

		$response = $this->actingAs($user)
			->get(route('authors.sales.request', $author))
			->assertOk()
			->assertViewHas(['isEnoughBooksTextCharacters' => false])
			->assertSeeText(__('author_sale_request.to_submit_a_request_your_added_books_must_have_at_least_two_characters_of_text_in_total', ['characters_count' => config('litlife.the_total_number_of_characters_of_the_authors_books_in_order_to_be_allowed_to_send_a_request_for_permission_to_sell_books')]));

		$response = $this->actingAs($user)
			->post(route('authors.sales.store', $author))
			->assertRedirect();

		$this->assertSessionHasErrors(__('author_sale_request.please_add_another_book_to_reach_the_required_number_of_characters'));
	}

	public function testCanSentRequestIfEnoughBooksCharactersCount()
	{
		$author = factory(Author::class)
			->states('with_author_manager', 'with_book')
			->create();

		$manager = $author->managers()->first();
		$book = $author->books()->first();
		$user = $manager->user;

		config(['litlife.the_total_number_of_characters_of_the_authors_books_in_order_to_be_allowed_to_send_a_request_for_permission_to_sell_books' => 1000]);

		$book->characters_count = 1001;
		$book->create_user()->associate($manager->user);
		$book->save();

		$response = $this->actingAs($user)
			->get(route('authors.sales.request', $author))
			->assertOk()
			->assertViewHas(['isEnoughBooksTextCharacters' => true])
			->assertDontSeeText(__('author_sale_request.to_submit_a_request_your_added_books_must_have_at_least_two_characters_of_text_in_total', ['characters_count' => config('litlife.the_total_number_of_characters_of_the_authors_books_in_order_to_be_allowed_to_send_a_request_for_permission_to_sell_books')]));

		$response = $this->actingAs($user)
			->post(route('authors.sales.store', $author), [
				'text' => $this->faker->realText(10000),
				'rules_accepted' => true
			])
			->assertRedirect()
			->assertSessionHasNoErrors();

		$sales_request = $author->sales_request()
			->first();

		$this->assertNotNull($sales_request);
	}

	public function testSeeYourAuthorPageMustHaveAtLeastOneBookAddedByYouError()
	{
		$author = factory(Author::class)
			->states('with_author_manager', 'with_book')
			->create();

		$manager = $author->managers()->first();
		$book = $author->books()->first();
		$user = $manager->user;
		$book->ready_status = 'complete';
		$book->save();

		$response = $this->actingAs($user)
			->get(route('authors.sales.request', $author))
			->assertOk()
			->assertViewHas(['authorHasBooksAddedByAuthUser' => false])
			->assertSeeText(__('author_sale_request.your_author_page_must_have_at_least_one_book_added_by_you'));

		$response = $this->actingAs($user)
			->post(route('authors.sales.store', $author), [
				'text' => $this->faker->realText(10000),
				'rules_accepted' => true
			])
			->assertRedirect();

		$this->assertSessionHasErrors(__('author_sale_request.your_author_page_must_have_at_least_one_book_added_by_you'));
	}
}
