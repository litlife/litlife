<?php

namespace Tests\Feature\Author\Manager;

use App\Author;
use App\AuthorSaleRequest;
use App\Manager;
use App\Notifications\AuthorManagerAcceptedNotification;
use App\Notifications\AuthorManagerRejectedNotification;
use App\User;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class AuthorManagerTest extends TestCase
{
	public function testIndexHttp()
	{
		$this->get(route('authors'))
			->assertOk();
	}

	public function testVerificationRequestHttp()
	{
		$author = factory(Author::class)
			->create();

		$user = factory(User::class)->create();
		$user->group->author_editor_request = true;
		$user->push();

		$this->actingAs($user)
			->get(route('authors.verification.request', $author))
			->assertOk();

		$comment = $this->faker->realText(100);

		$this->actingAs($user)
			->post(route('authors.verification.request_save', $author),
				['comment' => $comment])
			->assertSessionHasNoErrors()
			->assertRedirect(route('authors.show', $author))
			->assertSessionHas(['success' => __('manager.request_has_been_sent')]);

		$manager = $author->managers()->first();

		$this->assertNotNull($manager);
		$this->assertEquals($user->id, $manager->create_user_id);
		$this->assertEquals($user->id, $manager->user_id);
		$this->assertEquals('author', $manager->character);
		$this->assertEquals($author->id, $manager->manageable_id);
		$this->assertEquals('author', $manager->manageable_type);
		$this->assertEquals($comment, $manager->comment);
		$this->assertTrue($manager->isSentForReview());
	}

	/*
		public function testEditorRequestHttp()
		{
			$author = factory(Author::class)
				->create();

			$user = factory(User::class)->create();
			$user->group->author_editor_request = true;
			$user->push();

			$this->actingAs($user)
				->get(route('authors.editor.request', $author))
				->assertOk();

			$comment = $this->faker->realText(100);

			$this->actingAs($user)
				->post(route('authors.editor.request_save', $author),
					['comment' => $comment])
				->assertSessionHasNoErrors()
				->assertRedirect(route('authors.show', $author))
				->assertSessionHas(['success' => __('manager.request_has_been_sent')]);

			$manager = $author->managers()->first();

			$this->assertNotNull($manager);
			$this->assertEquals($user->id, $manager->create_user_id);
			$this->assertEquals($user->id, $manager->user_id);
			$this->assertEquals('editor', $manager->character);
			$this->assertEquals($author->id, $manager->manageable_id);
			$this->assertEquals('author', $manager->manageable_type);
			$this->assertEquals($comment, $manager->comment);
			$this->assertTrue($manager->isSentForReview());
		}
	*/

	public function testAcceptHttp()
	{
		Notification::fake();

		$admin = factory(User::class)
			->create();
		$admin->group->author_editor_check = true;
		$admin->push();

		$manager = factory(Manager::class)
			->states(['author', 'starts_review'])
			->create();
		$manager->status_changed_user_id = $admin->id;
		$manager->save();
		$manager->refresh();

		$this->assertTrue($manager->isReviewStarts());
		$this->assertEquals('author', $manager->character);

		$this->actingAs($admin)
			->get(route('managers.approve', ['manager' => $manager->id]))
			->assertRedirect(route('managers.on_check'))
			->assertSessionHas(['success' => __('manager.request_approved')]);

		$manager->refresh();

		$this->assertTrue($manager->isAccepted());

		Notification::assertSentTo(
			$manager->user,
			AuthorManagerAcceptedNotification::class,
			function ($notification, $channels) use ($manager) {
				$this->assertContains('mail', $channels);
				$this->assertContains('database', $channels);

				$mail = $notification->toMail($manager->user);

				$this->assertEquals(__('notification.author_manager_request_accepted.subject'), $mail->subject);
				$this->assertEquals(__('notification.author_manager_request_accepted.line', ['author_name' => $manager->manageable->name]), $mail->introLines[0]);
				$this->assertEquals(__('notification.author_manager_request_accepted.action'), $mail->actionText);
				$this->assertEquals(route('authors.show', ['author' => $manager->manageable]), $mail->actionUrl);

				$array = $notification->toArray($manager->user);

				$this->assertEquals(__('notification.author_manager_request_accepted.subject'), $array['title']);
				$this->assertEquals(__('notification.author_manager_request_accepted.line', ['author_name' => $manager->manageable->name]), $array['description']);
				$this->assertEquals(route('authors.show', ['author' => $manager->manageable]), $array['url']);

				return $notification->manager->id == $manager->id;
			}
		);
	}

	public function testRejectHttp()
	{
		Notification::fake();

		$admin = factory(User::class)
			->create();
		$admin->group->author_editor_check = true;
		$admin->push();

		$manager = factory(Manager::class)
			->states(['author', 'starts_review'])
			->create();
		$manager->status_changed_user_id = $admin->id;
		$manager->save();
		$manager->refresh();

		$this->assertTrue($manager->isReviewStarts());
		$this->assertEquals('author', $manager->character);

		$this->actingAs($admin)
			->get(route('managers.decline', ['manager' => $manager->id]))
			->assertRedirect(route('managers.on_check'))
			->assertSessionHas(['success' => __('manager.declined')]);

		$manager->refresh();

		$this->assertTrue($manager->isRejected());

		$user = $manager->user;

		$this->assertNull($user->groups()->whereName('Автор')->first());

		Notification::assertSentTo(
			$manager->user,
			AuthorManagerRejectedNotification::class,
			function ($notification, $channels) use ($manager) {
				$this->assertContains('mail', $channels);
				$this->assertContains('database', $channels);

				$mail = $notification->toMail($manager->user);

				$this->assertEquals(__('notification.author_manager_request_rejected.subject'), $mail->subject);
				$this->assertEquals(__('notification.author_manager_request_rejected.line', ['author_name' => $manager->manageable->name]), $mail->introLines[0]);
				$this->assertEquals(__('notification.author_manager_request_rejected.action'), $mail->actionText);
				$this->assertEquals(route('authors.show', ['author' => $manager->manageable]), $mail->actionUrl);

				$array = $notification->toArray($manager->user);

				$this->assertEquals(__('notification.author_manager_request_rejected.subject'), $array['title']);
				$this->assertEquals(__('notification.author_manager_request_rejected.line', ['author_name' => $manager->manageable->name]), $array['description']);
				$this->assertEquals(route('authors.show', ['author' => $manager->manageable]), $array['url']);

				return $notification->manager->id == $manager->id;
			}
		);
	}

	public function testProfitPercentAttribute()
	{
		$comission = rand(10, 90);

		config(['litlife.comission' => $comission]);

		$manager = factory(Manager::class)
			->states('author')
			->create();

		$this->assertEquals($manager->profit_percent, 100 - $comission);

		$profit_percent = rand(10, 90);

		$manager->profit_percent = $profit_percent;
		$manager->save();

		$this->assertEquals($manager->profit_percent, $profit_percent);
	}

	public function testIsAuthorEditor()
	{
		$manager = factory(Manager::class)
			->states('character_author')
			->create();

		$this->assertTrue($manager->isAuthorCharacter());
		$this->assertFalse($manager->isEditorCharacter());

		$manager = factory(Manager::class)
			->states('character_editor')
			->create();

		$this->assertFalse($manager->isAuthorCharacter());
		$this->assertTrue($manager->isEditorCharacter());
	}

	public function testAttachAuthorUserGroupOnApprove()
	{
		$admin = factory(User::class)->states('admin')->create();

		$manager = factory(Manager::class)
			->states(['author', 'starts_review'])
			->create();
		$manager->status_changed_user_id = $admin->id;
		$manager->save();
		$manager->refresh();

		$this->actingAs($admin)
			->get(route('managers.approve', ['manager' => $manager->id]))
			->assertRedirect(route('managers.on_check'))
			->assertSessionHas(['success' => __('manager.request_approved')]);

		$manager->refresh();

		$this->assertTrue($manager->isAccepted());

		$user = $manager->user;

		$this->assertEquals('Автор', $user->groups()->disableCache()->whereName('Автор')->first()->name);
	}

	public function testDetachAuthorUserGroupOnManagerDelete()
	{
		$admin = factory(User::class)->states('admin')->create();

		$manager = factory(Manager::class)
			->states(['author', 'accepted'])
			->create();

		$user = $manager->user;
		$user->attachUserGroupByNameIfExists('Автор');

		$this->assertEquals('Автор', $user->groups()->disableCache()->whereName('Автор')->first()->name);
		$this->assertEquals(2, $user->groups()->disableCache()->count());

		$this->actingAs($admin)
			->get(route('managers.destroy', ['manager' => $manager->id]))
			->assertRedirect();

		$manager->refresh();
		$user->refresh();

		$this->assertSoftDeleted($manager);
		$this->assertNull($user->groups()->disableCache()->whereName('Автор')->first());
		$this->assertEquals(1, $user->groups()->disableCache()->count());
	}

	public function testStartReview()
	{
		$admin = factory(User::class)
			->states('admin')
			->create();

		$manager = factory(Manager::class)
			->states('on_review')
			->create();

		$this->actingAs($admin)
			->get(route('managers.start_review', $manager))
			->assertRedirect();

		$manager->refresh();

		$this->assertTrue($manager->isReviewStarts());
	}

	public function testStopReview()
	{
		$admin = factory(User::class)
			->states('admin')
			->create();

		$manager = factory(Manager::class)
			->states('starts_review')
			->create();
		$manager->status_changed_user_id = $admin->id;
		$manager->save();
		$manager->refresh();

		$this->assertNotNull($manager->status_changed_user_id);

		$this->assertTrue($manager->isReviewStarts());

		$this->actingAs($admin)
			->get(route('managers.stop_review', $manager))
			->assertRedirect();

		$manager->refresh();

		$this->assertTrue($manager->isSentForReview());
	}

	public function testPolicyIsCantStartReviewIfAlreadyReviewStarts()
	{
		$admin = factory(User::class)
			->states('admin')
			->create();

		$manager = factory(Manager::class)
			->states('starts_review')
			->create();

		$this->assertFalse($admin->can('startReview', $manager));
	}

	public function testPolicyIsCantStartReviewIfAccepted()
	{
		$admin = factory(User::class)
			->states('admin')
			->create();

		$manager = factory(Manager::class)
			->states('accepted')
			->create();

		$this->assertFalse($admin->can('startReview', $manager));
	}

	public function testPolicyIsCantStartReviewIfRejected()
	{
		$admin = factory(User::class)
			->states('admin')
			->create();

		$manager = factory(Manager::class)
			->states('rejected')
			->create();

		$this->assertFalse($admin->can('startReview', $manager));
	}

	public function testPolicyIsCantStopReviewIfRejected()
	{
		$admin = factory(User::class)
			->states('admin')
			->create();

		$manager = factory(Manager::class)
			->states('rejected')
			->create();

		$this->assertFalse($admin->can('stopReview', $manager));
	}

	public function testPolicyIsCantStopReviewIfAccepted()
	{
		$admin = factory(User::class)
			->states('admin')
			->create();

		$manager = factory(Manager::class)
			->states('rejected')
			->create();

		$this->assertFalse($admin->can('stopReview', $manager));
	}

	public function testPolicyIsCantStopReviewIfOtherUserStarts()
	{
		$admin = factory(User::class)
			->states('admin')
			->create();

		$manager = factory(Manager::class)
			->states('starts_review')
			->create();

		$this->assertFalse($admin->can('stopReview', $manager));
	}

	public function testPolicyIsCantApproveIfOtherUserStarts()
	{
		$admin = factory(User::class)
			->states('admin')
			->create();

		$manager = factory(Manager::class)
			->states('starts_review')
			->create();

		$this->assertFalse($admin->can('approve', $manager));
	}

	public function testPolicyIsCantDeclineIfOtherUserStarts()
	{
		$admin = factory(User::class)
			->states('admin')
			->create();

		$manager = factory(Manager::class)
			->states('starts_review')
			->create();

		$this->assertFalse($admin->can('decline', $manager));
	}

	public function testPolicyCanApprove()
	{
		$admin = factory(User::class)
			->states('admin')
			->create();

		$manager = factory(Manager::class)
			->states('starts_review')
			->create();
		$manager->status_changed_user_id = $admin->id;
		$manager->save();
		$manager->refresh();

		$this->assertTrue($admin->can('approve', $manager));
	}

	public function testPolicyCanDecline()
	{
		$admin = factory(User::class)
			->states('admin')
			->create();

		$manager = factory(Manager::class)
			->states('starts_review')
			->create();
		$manager->status_changed_user_id = $admin->id;
		$manager->save();
		$manager->refresh();

		$this->assertTrue($admin->can('decline', $manager));
	}

	public function testPolicyCanStopReview()
	{
		$admin = factory(User::class)
			->states('admin')
			->create();

		$manager = factory(Manager::class)
			->states('starts_review')
			->create();
		$manager->status_changed_user_id = $admin->id;
		$manager->save();
		$manager->refresh();

		$this->assertTrue($admin->can('stopReview', $manager));
	}

	public function testSentVerificationRequestIfAuthorPrivate()
	{
		$author = factory(Author::class)
			->states('private')
			->create();

		$user = $author->create_user;
		$user->group->author_editor_request = true;
		$user->push();

		$comment = $this->faker->realText(300);

		$this->actingAs($user)
			->post(route('authors.verification.request_save', $author), [
				'comment' => $comment
			])
			->assertSessionHasNoErrors()
			->assertRedirect()
			->assertSessionHas(['success' => __('manager.request_is_saved_and_will_be_sent_for_review_after_the_authors_publication')]);

		$manager = $author->managers()->first();

		$this->assertNotNull($manager);
		$this->assertTrue($manager->isPrivate());
	}

	/*
		public function testSentEditorRequestIfAuthorPrivate()
		{
			$author = factory(Author::class)
				->states('private')
				->create();

			$user = $author->create_user;
			$user->group->author_editor_request = true;
			$user->push();

			$comment = $this->faker->realText(300);

			$this->actingAs($user)
				->post(route('authors.editor.request_save', $author), [
					'comment' => $comment
				])
				->assertSessionHasNoErrors()
				->assertRedirect()
				->assertSessionHas(['success' => __('manager.request_is_saved_and_will_be_sent_for_review_after_the_authors_publication')]);

			$manager = $author->managers()->first();

			$this->assertNotNull($manager);
			$this->assertTrue($manager->isPrivate());
		}
	*/
	public function testCantApproveIfAuthorIsNotPublished()
	{
		$admin = factory(User::class)
			->states('admin')
			->create();

		$manager = factory(Manager::class)
			->states('starts_review')
			->create();
		$manager->status_changed_user_id = $admin->id;
		$manager->save();
		$manager->refresh();

		$manager->manageable->statusPrivate();
		$manager->push();

		$this->assertTrue($manager->manageable->isPrivate());

		$this->assertFalse($admin->can('approve', $manager));
		$this->assertFalse($admin->can('decline', $manager));
	}

	public function testCantStartsReviewIfAuthorIsNotPublished()
	{
		$admin = factory(User::class)
			->states('admin')
			->create();

		$manager = factory(Manager::class)
			->states('on_review')
			->create();

		$manager->manageable->statusPrivate();
		$manager->push();

		$this->assertTrue($manager->manageable->isPrivate());

		$this->assertFalse($admin->can('startReview', $manager));
	}


	public function testSalesDisableHttp()
	{
		$author = factory(Author::class)
			->states('with_author_manager_can_sell', 'with_book_for_sale')
			->create();

		$manager = $author->managers()->first();
		$book = $author->books()->first();
		$seller = $manager->user;
		$book->create_user_id = $seller->id;
		$book->save();
		$book->refresh();

		$user = factory(User::class)
			->create();

		$this->assertFalse($user->can('read', $book));
		$this->assertTrue($user->can('buy', $book));
		$this->assertTrue($seller->can('sell', $book));

		$admin = factory(User::class)
			->states('admin')
			->create();

		$this->actingAs($admin)
			->get(route('authors.sales.disable', $author))
			->assertRedirect(route('authors.show', $author))
			->assertSessionHas(['success' => __('manager.ability_to_sell_books_for_the_author_is_disabled')]);

		$manager->refresh();
		$book->refresh();

		$this->assertFalse($user->can('buy', $book));
		$this->assertFalse($seller->can('sell', $book));
		$this->assertFalse($manager->can_sale);
		$this->assertEquals(0, $book->price);
		$this->assertFalse($user->can('read', $book));
	}

	public function testCantSalesDisableIfNoPermissions()
	{
		$author = factory(Author::class)
			->states('with_author_manager_can_sell', 'with_book_for_sale')
			->create();

		$manager = $author->managers()->first();
		$book = $author->books()->first();
		$seller = $manager->user;
		$book->create_user_id = $seller->id;
		$book->save();
		$book->refresh();

		$admin = factory(User::class)
			->states('admin')
			->create();

		$this->assertTrue($admin->can('salesDisable', $author));

		$admin->group->author_sale_request_review = false;
		$admin->push();
		$admin->refresh();

		$this->assertFalse($admin->can('salesDisable', $author));
	}

	public function testRemoveSaleRequestIfManagerDestroyed()
	{
		$admin = factory(User::class)->states('admin')->create();

		$manager = factory(Manager::class)
			->states(['author', 'accepted'])
			->create();

		$user = $manager->user;
		$author = $manager->manageable;

		$saleRequest = factory(AuthorSaleRequest::class)
			->states('on_review')
			->create([
				'create_user_id' => $user,
				'manager_id' => $manager->id,
				'author_id' => $author->id
			]);

		$this->actingAs($admin)
			->get(route('managers.destroy', ['manager' => $manager->id]))
			->assertRedirect();

		$manager->refresh();
		$user->refresh();
		$saleRequest->refresh();

		$this->assertSoftDeleted($manager);
		$this->assertSoftDeleted($saleRequest);
	}


	public function testCanStartReviewIfAuthorDeleted()
	{
		$admin = factory(User::class)
			->states('admin')
			->create();

		$manager = factory(Manager::class)
			->states(['author', 'on_review'])
			->create();

		$manager->manageable->delete();
		$manager->refresh();

		$this->assertTrue($admin->can('startReview', $manager));

		$manager->manageable()->forceDelete();
		$manager->refresh();

		$this->assertTrue($admin->can('startReview', $manager));
	}

	public function testCantApproveIfAuthorDeleted()
	{
		$admin = factory(User::class)
			->states('admin')
			->create();

		$manager = factory(Manager::class)
			->states(['author', 'starts_review'])
			->create();
		$manager->status_changed_user_id = $admin->id;
		$manager->save();
		$manager->refresh();

		$manager->manageable->delete();
		$manager->refresh();

		$this->assertFalse($admin->can('startReview', $manager));

		$manager->manageable()->forceDelete();
		$manager->refresh();

		$this->assertFalse($admin->can('startReview', $manager));
	}

	public function testCanDeclineIfAuthorDeleted()
	{
		$admin = factory(User::class)
			->states('admin')
			->create();

		$manager = factory(Manager::class)
			->states(['author', 'starts_review'])
			->create();
		$manager->status_changed_user_id = $admin->id;
		$manager->save();
		$manager->refresh();

		$manager->manageable->delete();
		$manager->refresh();

		$this->assertTrue($admin->can('decline', $manager));

		$manager->manageable()->forceDelete();
		$manager->refresh();

		$this->assertTrue($admin->can('decline', $manager));
	}

	public function testCanStopReviewIfAuthorDeleted()
	{
		$admin = factory(User::class)
			->states('admin')
			->create();

		$manager = factory(Manager::class)
			->states(['author', 'starts_review'])
			->create();
		$manager->status_changed_user_id = $admin->id;
		$manager->save();
		$manager->refresh();

		$manager->manageable->delete();
		$manager->refresh();

		$this->assertTrue($admin->can('stopReview', $manager));

		$manager->manageable()->forceDelete();
		$manager->refresh();

		$this->assertTrue($admin->can('stopReview', $manager));
	}

	public function testDeletePrivateManagerRequestIfAuthorDeleteOrRestore()
	{
		$admin = factory(User::class)
			->states('admin')
			->create();

		$manager = factory(Manager::class)
			->states(['author', 'private'])
			->create();

		$author = $manager->manageable;
		$author->delete();
		$manager->refresh();

		$this->assertTrue($manager->trashed());

		$author->restore();
		$manager->refresh();

		$this->assertFalse($manager->trashed());
	}

	public function testIsUserVerifiedAuthorOfBookIsTrue()
	{
		$author = factory(Author::class)
			->states('with_author_manager', 'with_book')
			->create();

		$manager = $author->managers()->first();
		$user = $manager->user;
		$book = $author->books()->first();

		$this->assertTrue($book->isUserVerifiedAuthorOfBook($user));
	}

	public function testIsUserVerifiedAuthorOfBookIsFalseIfManagerIsEditor()
	{
		$author = factory(Author::class)
			->states('with_editor_manager', 'with_book')
			->create();

		$manager = $author->managers()->first();
		$book = $author->books()->first();
		$user = $manager->user;

		$this->assertFalse($book->isUserVerifiedAuthorOfBook($user));
	}

	public function testIsUserVerifiedAuthorOfBookIsFalseIfManagerIsNotVerified()
	{
		$author = factory(Author::class)
			->states('with_author_manager', 'with_book')
			->create();

		$manager = $author->managers()->first();
		$book = $author->books()->first();
		$user = $manager->user;
		$manager->statusSentForReview();
		$manager->save();

		$this->assertFalse($book->isUserVerifiedAuthorOfBook($user));
	}

	public function testCantChangeSiLPPublishFieldsIfBookAccepted()
	{
		$author = factory(Author::class)
			->states('with_author_manager', 'with_si_book')
			->create();

		$manager = $author->managers()->first();
		$book = $author->books()->first();
		$user = $manager->user;

		$this->actingAs($user)
			->get(route('books.edit', $book))
			->assertOk()
			->assertViewHas(['cantEditSiLpPublishFields' => true]);

		$post = [
			'title' => $book->title,
			'genres' => [$book->genres()->first()->id],
			'writers' => [$book->writers()->first()->id],
			'ti_lb' => 'RU',
			'ti_olb' => 'RU',
			'ready_status' => 'complete',
			'is_si' => false,
			'is_lp' => false,
			'pi_pub' => $this->faker->realText(50),
			'pi_city' => $this->faker->realText(50)
		];

		$this->actingAs($user)
			->patch(route('books.update', $book), $post)
			->assertRedirect()
			->assertSessionHasNoErrors();

		$book->refresh();

		$this->assertTrue($book->is_si);
		$this->assertEmpty($book->pi_pub);
		$this->assertEmpty($book->pi_city);
	}
}
