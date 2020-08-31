<?php

namespace Tests\Feature\Book;

use App\Author;
use App\Book;
use App\BookFile;
use App\BookKeyword;
use App\Enums\StatusEnum;
use App\Jobs\Sequence\UpdateSequenceBooksCount;
use App\Manager;
use App\Notifications\BookPublishedNotification;
use App\Sequence;
use App\User;
use App\UserOnModeration;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class BookPublishTest extends TestCase
{
	public function testAddForReviewHttp()
	{
		config(['activitylog.enabled' => true]);

		$this->resetCounters();

		$this->assertEquals(0, Book::getCachedOnModerationCount());
		$this->assertEquals(0, BookFile::getCachedOnModerationCount());

		$admin = factory(User::class)->create();
		$admin->group->check_books = true;
		$admin->push();

		$user = factory(User::class)->create();
		$user->group->add_book = true;
		$user->group->add_book_without_check = false;
		$user->group->check_books = false;
		$user->push();

		$book = factory(Book::class)
			->states('with_writer', 'private', 'with_section', 'lp_false')
			->create(['create_user_id' => $user->id]);

		$book->authors()->detach();
		$book->sequences()->detach();

		$author = factory(Author::class)->create(['create_user_id' => $user->id]);
		$author->statusPrivate();
		$author->save();

		$illustrator = factory(Author::class)->create(['create_user_id' => $user->id]);
		$illustrator->statusPrivate();
		$illustrator->save();

		$translator = factory(Author::class)->create(['create_user_id' => $user->id]);
		$translator->statusPrivate();
		$translator->save();

		$book->writers()->sync([$author->id]);
		$book->translators()->sync([$translator->id]);
		$book->illustrators()->sync([$illustrator->id]);

		$sequence = factory(Sequence::class)->create(['create_user_id' => $user->id]);
		$sequence->statusPrivate();
		$sequence->save();

		$book->sequences()->sync([$sequence->id]);

		UpdateSequenceBooksCount::dispatch($sequence);

		$this->assertEquals(1, $sequence->fresh()->book_count);

		$book_file = factory(BookFile::class)
			->states('txt')
			->create(['book_id' => $book->id, 'create_user_id' => $user->id]);
		$book_file->statusPrivate();
		$book_file->save();

		$book_keyword = factory(BookKeyword::class)
			->create(['book_id' => $book->id, 'create_user_id' => $user->id]);
		$book_keyword->statusPrivate();
		$book_keyword->save();

		$response = $this->followingRedirects()
			->actingAs($user)
			->get(route('books.publish', $book));

		//dump(session('errors'));
		$response->assertOk()
			->assertSessionHasNoErrors()
			->assertSeeText(__('book.added_for_check'));

		$this->actingAs($admin)
			->get(route('books.show', $book))
			->assertOk()
			->assertSeeText($book->title)
			->assertSeeText($author->name)
			->assertSeeText($illustrator->name)
			->assertSeeText($translator->name)
			->assertSeeText($sequence->name)
			->assertSeeText($book_keyword->keyword->text);

		$sequence->refresh();
		$book->refresh();

		$this->assertEquals(1, Book::getCachedOnModerationCount());
		$this->assertEquals(1, BookFile::getCachedOnModerationCount());

		$this->assertEquals(StatusEnum::OnReview, $book->fresh()->status);
		$this->assertEquals(StatusEnum::OnReview, $author->fresh()->status);
		$this->assertEquals(StatusEnum::OnReview, $illustrator->fresh()->status);
		$this->assertEquals(StatusEnum::OnReview, $translator->fresh()->status);
		$this->assertEquals(StatusEnum::OnReview, $book_file->fresh()->status);
		$this->assertEquals(StatusEnum::OnReview, $book_keyword->fresh()->status);
		$this->assertTrue($book->isSentForReview());
		$this->assertTrue($sequence->isSentForReview());

		$this->assertEquals(0, $sequence->book_count);

		$this->assertNull($book_file->formats);

		$this->assertEquals(1, $book->activities()->count());
		$activity = $book->activities()->first();
		$this->assertEquals('add_for_review', $activity->description);
		$this->assertEquals($user->id, $activity->causer_id);
		$this->assertEquals('user', $activity->causer_type);

		$this->assertFalse($book->isReadAccess());
		$this->assertFalse($book->isDownloadAccess());
	}

	public function resetCounters()
	{
		Book::sentOnReview()->update(['status' => StatusEnum::Accepted]);
		Book::flushCachedOnModerationCount();
		$this->assertEquals(0, Book::getCachedOnModerationCount());

		BookFile::sentOnReview()->update(['status' => StatusEnum::Accepted]);
		BookFile::flushCachedOnModerationCount();
		$this->assertEquals(0, BookFile::getCachedOnModerationCount());

		BookKeyword::sentOnReview()->update(['status' => StatusEnum::Accepted]);
		BookKeyword::flushCachedOnModerationCount();
		$this->assertEquals(0, BookKeyword::getCachedOnModerationCount());
	}

	public function testMakeAcceptedHttp()
	{
		config(['activitylog.enabled' => true]);

		$this->resetCounters();

		Notification::fake();

		$this->assertEquals(0, Book::getCachedOnModerationCount());
		$this->assertEquals(0, BookFile::getCachedOnModerationCount());

		$admin = factory(User::class)->create();
		$admin->group->check_books = true;
		$admin->group->add_book_without_check = false;
		$admin->group->edit_other_user_book = true;
		$admin->push();

		$user = factory(User::class)->create();

		$book = factory(Book::class)
			->states('sent_for_review', 'with_section', 'with_file', 'lp_false')
			->create(['create_user_id' => $user->id]);

		$book->authors()->detach();
		$book->sequences()->detach();

		$author = factory(Author::class)->create(['create_user_id' => $user->id]);
		$author->statusSentForReview();
		$author->save();

		$illustrator = factory(Author::class)->create(['create_user_id' => $user->id]);
		$illustrator->statusSentForReview();
		$illustrator->save();

		$translator = factory(Author::class)->create(['create_user_id' => $user->id]);
		$translator->statusSentForReview();
		$translator->save();

		$book->writers()->sync([$author->id]);
		$book->translators()->sync([$translator->id]);
		$book->illustrators()->sync([$illustrator->id]);

		$sequence = factory(Sequence::class)->create(['create_user_id' => $user->id]);
		$sequence->statusPrivate();
		$sequence->save();

		$book->sequences()->sync([$sequence->id]);

		UpdateSequenceBooksCount::dispatch($sequence);
		$sequence->refresh();

		$this->assertEquals(1, $sequence->book_count);

		$book_file = factory(BookFile::class)->states('txt')->create(['book_id' => $book->id, 'create_user_id' => $user->id]);
		$book_file->statusSentForReview();
		$book_file->save();

		$book_keyword = factory(BookKeyword::class)->create(['book_id' => $book->id, 'create_user_id' => $user->id]);
		$book_keyword->statusSentForReview();
		$book_keyword->save();

		$this->assertTrue($admin->can('makeAccepted', $book));

		Book::flushCachedOnModerationCount();
		$this->assertEquals(1, Book::getCachedOnModerationCount());
		BookFile::flushCachedOnModerationCount();
		$this->assertEquals(1, BookFile::getCachedOnModerationCount());
		BookKeyword::flushCachedOnModerationCount();
		$this->assertEquals(1, BookKeyword::getCachedOnModerationCount());

		$response = $this->followingRedirects()
			->actingAs($admin)
			->get(route('books.publish', $book))
			->assertSessionHasNoErrors()
			->assertOk()
			->assertSeeText(__('book.published'));

		$sequence->refresh();
		$book->refresh();

		$this->actingAs($admin)
			->get(route('books.show', $book))
			->assertOk()
			->assertSeeText($book->title)
			->assertSeeText($author->name)
			->assertSeeText($illustrator->name)
			->assertSeeText($translator->name)
			->assertSeeText($sequence->name)
			->assertSeeText($book_keyword->keyword->text);

		$this->assertEquals(StatusEnum::Accepted, $book->fresh()->status);
		$this->assertEquals(StatusEnum::Accepted, $author->fresh()->status);
		$this->assertEquals(StatusEnum::Accepted, $illustrator->fresh()->status);
		$this->assertEquals(StatusEnum::Accepted, $translator->fresh()->status);
		$this->assertEquals(StatusEnum::Accepted, $book_file->fresh()->status);
		$this->assertEquals(StatusEnum::Accepted, $book_keyword->fresh()->status);

		$this->assertEquals(1, $sequence->book_count);

		$this->assertEquals([$book_file->extension], $book->formats);

		$this->assertEquals(0, Book::getCachedOnModerationCount());
		$this->assertEquals(0, BookFile::getCachedOnModerationCount());
		$this->assertEquals(0, BookKeyword::getCachedOnModerationCount());

		$this->assertEquals(1, $book->activities()->count());
		$activity = $book->activities()->first();
		$this->assertEquals('make_accepted', $activity->description);
		$this->assertEquals($admin->id, $activity->causer_id);
		$this->assertEquals('user', $activity->causer_type);

		$this->assertFalse($book->isReadAccess());
		$this->assertFalse($book->isDownloadAccess());

		Notification::assertSentTo(
			$book->create_user,
			BookPublishedNotification::class,
			function ($notification, $channels) use ($book) {
				$this->assertContains('mail', $channels);
				$this->assertContains('database', $channels);

				$mail = $notification->toMail($book->create_user);

				$this->assertEquals(__('notification.book_published.subject'), $mail->subject);

				$this->assertEquals(__('notification.book_published.line', [
					'book_title' => $book->title,
					'writers_names' => implode(', ', $book->writers->pluck('name')->toArray())
				]), $mail->introLines[0]);

				$this->assertEquals(__('notification.book_published.action'), $mail->actionText);

				$this->assertEquals(route('books.show', ['book' => $book]), $mail->actionUrl);

				return $notification->book->id == $book->id;
			}
		);
	}

	public function testPublishIfCreatorCanAddWithoutCheck()
	{
		$this->resetCounters();

		Notification::fake();

		$this->assertEquals(0, Book::getCachedOnModerationCount());
		$this->assertEquals(0, BookFile::getCachedOnModerationCount());

		$book = factory(Book::class)
			->states('with_writer', 'private', 'with_section', 'with_file', 'lp_false')
			->create();

		$user = $book->create_user;
		$user->group->check_books = false;
		$user->group->add_book_without_check = true;
		$user->group->edit_other_user_book = false;
		$user->push();

		$response = $this->followingRedirects()
			->actingAs($user)
			->get(route('books.publish', $book))
			->assertSessionHasNoErrors()
			->assertOk()
			->assertSeeText(__('book.published'));

		$book->refresh();

		$this->assertTrue($book->isAccepted());

		$this->assertFalse($book->isReadAccess());
		$this->assertFalse($book->isDownloadAccess());

		Notification::assertNotSentTo(
			$book->create_user, BookPublishedNotification::class
		);
	}

	public function testMakePrivateToAccepted()
	{
		$this->resetCounters();

		$admin = factory(User::class)->create();
		$admin->group->check_books = true;
		$admin->group->add_book_without_check = true;
		$admin->group->edit_other_user_book = true;
		$admin->push();

		$user = factory(User::class)->create();

		$book = factory(Book::class)
			->states('private', 'with_section', 'with_file', 'lp_false')
			->create(['create_user_id' => $user->id]);

		$book->authors()->detach();
		$book->sequences()->detach();

		$author = factory(Author::class)->create(['create_user_id' => $user->id]);
		$author->statusPrivate();
		$author->save();

		$illustrator = factory(Author::class)->create(['create_user_id' => $user->id]);
		$illustrator->statusPrivate();
		$illustrator->save();

		$translator = factory(Author::class)->create(['create_user_id' => $user->id]);
		$translator->statusPrivate();
		$translator->save();

		$book->writers()->sync([$author->id]);
		$book->translators()->sync([$translator->id]);
		$book->illustrators()->sync([$illustrator->id]);

		$sequence = factory(Sequence::class)->create(['create_user_id' => $user->id]);
		$sequence->statusPrivate();
		$sequence->save();

		$book->sequences()->sync([$sequence->id]);

		UpdateSequenceBooksCount::dispatch($sequence);
		$sequence->refresh();

		$this->assertEquals(1, $sequence->book_count);

		$book_file = factory(BookFile::class)->states('txt')->create(['book_id' => $book->id, 'create_user_id' => $user->id]);
		$book_file->statusPrivate();
		$book_file->save();

		$book_keyword = factory(BookKeyword::class)->create(['book_id' => $book->id, 'create_user_id' => $user->id]);
		$book_keyword->statusPrivate();
		$book_keyword->save();

		$this->assertTrue($admin->can('makeAccepted', $book));

		$response = $this->followingRedirects()
			->actingAs($admin)
			->get(route('books.publish', $book));

		//dump(session('errors'));

		$response->assertSessionHasNoErrors()
			->assertOk()
			->assertSeeText(__('book.published'));

		$sequence->refresh();
		$book->refresh();

		$this->actingAs($admin)
			->get(route('books.show', $book))
			->assertOk()
			->assertSeeText($book->title)
			->assertSeeText($author->name)
			->assertSeeText($illustrator->name)
			->assertSeeText($translator->name)
			->assertSeeText($sequence->name)
			->assertSeeText($book_keyword->keyword->text);

		$this->assertEquals(StatusEnum::Accepted, $book->fresh()->status);
		$this->assertEquals(StatusEnum::Accepted, $author->fresh()->status);
		$this->assertEquals(StatusEnum::Accepted, $illustrator->fresh()->status);
		$this->assertEquals(StatusEnum::Accepted, $translator->fresh()->status);
		$this->assertEquals(StatusEnum::Accepted, $book_file->fresh()->status);
		$this->assertEquals(StatusEnum::Accepted, $book_keyword->fresh()->status);

		$this->assertEquals(1, $sequence->book_count);

		$this->assertEquals([$book_file->extension], $book->formats);

		$this->assertEquals(0, Book::getCachedOnModerationCount());
		$this->assertEquals(0, BookFile::getCachedOnModerationCount());

		$this->assertFalse($book->isReadAccess());
		$this->assertFalse($book->isDownloadAccess());
	}

	public function testPublishByUserOnModeate()
	{
		$this->resetCounters();

		$this->assertEquals(0, Book::getCachedOnModerationCount());
		$this->assertEquals(0, BookFile::getCachedOnModerationCount());

		$book = factory(Book::class)
			->states('with_writer', 'private', 'with_section', 'with_file', 'lp_false')
			->create();

		$user = $book->create_user;
		$user->group->check_books = false;
		$user->group->add_book_without_check = false;
		$user->group->edit_other_user_book = false;
		$user->push();

		$user_on_moderation = factory(UserOnModeration::class)
			->create(['user_id' => $user->id]);

		$user->refresh();

		$this->assertTrue($user->isOnModeration());

		$response = $this->followingRedirects()
			->actingAs($user)
			->get(route('books.publish', $book))
			->assertSessionHasNoErrors()
			->assertOk()
			->assertSeeText(__('book.on_check'))
			->assertSeeText(__('book.you_will_receive_a_notification_when_the_book_is_published'))
			->assertSeeText(__('book.added_for_check'));

		$book->refresh();

		$this->assertTrue($book->isUserChangedStatus($user));
		$this->assertTrue($book->isSentForReview());

		$this->assertFalse($book->isReadAccess());
		$this->assertFalse($book->isDownloadAccess());
	}

	public function testBookAcceptedIfPublishedByAuthorHttp()
	{
		$author = factory(Author::class)
			->states('with_author_manager', 'with_private_book')
			->create();

		$user = $author->managers->first()->user;
		$user->group->add_book = true;
		$user->push();

		$book = $author->books()->first();
		$book->create_user_id = $user->id;
		$book->is_lp = false;
		$book->save();

		$this->actingAs($user)
			->get(route('books.publish', $book))
			->assertRedirect(route('books.show', $book))
			->assertSessionHas('success', __('book.published'));

		$book->refresh();

		$this->assertTrue($book->isAccepted());

		$this->assertTrue($book->isReadAccess());
		$this->assertFalse($book->isDownloadAccess());
	}

	public function testBookSentForReviewIfPublishedByEditorHttp()
	{
		$author = factory(Author::class)
			->states('with_editor_manager', 'with_private_book')
			->create();

		$user = $author->managers->first()->user;
		$user->group->add_book = true;
		$user->push();

		$book = $author->books()->first();
		$book->create_user_id = $user->id;
		$book->is_lp = false;
		$book->save();

		$this->actingAs($user)
			->get(route('books.publish', $book))
			->assertRedirect(route('books.show', $book))
			->assertSessionHas('success', __('book.added_for_check'));

		$book->refresh();

		$this->assertTrue($book->isSentForReview());
		$this->assertFalse($book->isReadAccess());
		$this->assertFalse($book->isDownloadAccess());
	}

	public function testBookSentForReviewIfPublishedByNotAcceptedAuthorHttp()
	{
		$author = factory(Author::class)
			->states('with_author_manager', 'with_private_book')
			->create();

		$manager = $author->managers->first();
		$manager->statusSentForReview();
		$manager->save();

		$user = $manager->user;
		$user->group->add_book = true;
		$user->push();

		$book = $author->books()->first();
		$book->create_user_id = $user->id;
		$book->is_lp = false;
		$book->save();

		$response = $this->actingAs($user)
			->get(route('books.publish', $book));
		if (!empty(session('errors'))) var_dump(session('errors'));
		$response->assertRedirect(route('books.show', $book))
			->assertSessionHas('success', __('book.added_for_check'));

		$book->refresh();

		$this->assertTrue($book->isSentForReview());
		$this->assertFalse($book->isReadAccess());
		$this->assertFalse($book->isDownloadAccess());
	}

	public function testBookSentForReviewIfPublishedByAuthorButOtherAuthorExistsHttp()
	{
		$author = factory(Author::class)
			->states('with_author_manager', 'with_private_book')
			->create();

		$user = $author->managers->first()->user;
		$user->group->add_book = true;
		$user->push();

		$book = $author->books()->first();
		$book->create_user_id = $user->id;
		$book->is_lp = false;
		$book->save();

		$writer = factory(Author::class)->create();
		$book->writers()->sync([$writer->id]);
		$book->refresh();

		$this->actingAs($user)
			->get(route('books.publish', $book))
			->assertSessionHasNoErrors()
			->assertRedirect(route('books.show', $book))
			->assertSessionHas('success', __('book.added_for_check'));

		$book->refresh();

		$this->assertFalse($book->isReadAccess());
		$this->assertFalse($book->isDownloadAccess());
	}

	public function testPublishPolicy()
	{
		$book = factory(Book::class)
			->states('private', 'with_section', 'with_file')
			->create();

		$creator = $book->create_user;
		$creator->group->add_book_without_check = false;
		$creator->push();

		$admin = factory(User::class)->create();
		$admin->group->check_books = true;
		$admin->push();

		$editor = factory(User::class)->create();
		$editor->group->add_book_without_check = true;
		$editor->group->check_books = false;

		$editor->push();

		$this->assertTrue($creator->can('publish', $book));
		$this->assertTrue($admin->can('publish', $book));
		$this->assertFalse($editor->can('publish', $book));

		$book->statusSentForReview();
		$book->save();
		$book->refresh();

		$this->assertTrue($book->isSentForReview());
		$this->assertFalse($editor->can('publish', $book));
		$this->assertFalse($creator->can('publish', $book));
		$this->assertTrue($admin->can('publish', $book));

		$book->statusAccepted();
		$book->save();
		$book->refresh();

		$this->assertFalse($creator->can('publish', $book));
		$this->assertFalse($admin->can('publish', $book));
		$this->assertFalse($editor->can('publish', $book));
	}

	public function testSendToPrivateAndPublish()
	{
		$book = factory(Book::class)
			->states('with_writer', 'accepted', 'with_section', 'with_file', 'lp_false')
			->create();

		$admin = factory(User::class)
			->create();
		$admin->group->check_books = true;
		$admin->push();

		$this->actingAs($admin)
			->post(route('books.add_to_private', ['book' => $book]))
			->assertRedirect();

		$this->actingAs($admin)
			->get(route('books.publish', ['book' => $book]))
			->assertRedirect();

		$book->refresh();

		$this->assertFalse($book->isReadAccess());
		$this->assertFalse($book->isDownloadAccess());
	}

	public function testAuthorManagerSentForReviewOnBookPublish()
	{
		$admin = factory(User::class)
			->states('admin')
			->create();

		$book = factory(Book::class)
			->states('private', 'with_section', 'with_file', 'lp_false')
			->create();
		$user = $book->create_user;
		$user->group->add_book = true;
		$user->group->add_book_without_check = false;
		$user->group->check_books = false;
		$user->push();

		$manager = factory(Manager::class)
			->states('private', 'character_author')
			->create();

		$author = $manager->manageable;
		$book->authors()->sync([$author->id]);
		$book->save();

		$response = $this->followingRedirects()
			->actingAs($user)
			->get(route('books.publish', $book))
			->assertOk()
			->assertSessionHasNoErrors();

		$manager->refresh();
		$book->refresh();

		$this->assertTrue($manager->isSentForReview());
		$this->assertFalse($book->isReadAccess());
		$this->assertFalse($book->isDownloadAccess());
	}

	public function testDontDisableReadOrDownloadAccessForLpBooks()
	{
		$book = factory(Book::class)
			->states('with_writer', 'sent_for_review', 'with_section', 'with_file', 'lp_true')
			->create();

		$admin = factory(User::class)->create();
		$admin->group->check_books = true;
		$admin->push();

		$this->actingAs($admin)
			->get(route('books.publish', ['book' => $book]))
			->assertSessionHasNoErrors()
			->assertRedirect();

		$book->refresh();

		$this->assertTrue($book->isReadAccess());
		$this->assertTrue($book->isDownloadAccess());
	}

	public function testAccessEnabledIfSectionsExistsAfterPublish()
	{
		$book = factory(Book::class)
			->states('with_writer', 'private', 'with_section', 'lp_true')
			->create();

		$user = $book->create_user;
		$user->group->check_books = false;
		$user->group->add_book_without_check = true;
		$user->group->edit_other_user_book = false;
		$user->push();

		$this->assertEquals(0, $book->files()->count());
		$this->assertGreaterThan(0, $book->sections()->where('type', 'section')->count());

		$response = $this->followingRedirects()
			->actingAs($user)
			->get(route('books.publish', $book))
			->assertSessionHasNoErrors()
			->assertOk()
			->assertSeeText(__('book.published'));

		$book->refresh();

		$this->assertTrue($book->isAccepted());
		$this->assertTrue($book->isReadAccess());
		$this->assertFalse($book->isDownloadAccess());
	}

	public function testAccessEnabledIfFilesExistsAfterPublish()
	{
		$book = factory(Book::class)
			->states('with_writer', 'private', 'with_file', 'lp_true')
			->create();

		$user = $book->create_user;
		$user->group->check_books = false;
		$user->group->add_book_without_check = true;
		$user->group->edit_other_user_book = false;
		$user->push();

		$this->assertGreaterThan(0, $book->files()->count());
		$this->assertEquals(0, $book->sections()->where('type', 'section')->count());

		$response = $this->followingRedirects()
			->actingAs($user)
			->get(route('books.publish', $book))
			->assertSessionHasNoErrors()
			->assertOk()
			->assertSeeText(__('book.published'));

		$book->refresh();

		$this->assertTrue($book->isAccepted());
		$this->assertFalse($book->isReadAccess());
		$this->assertTrue($book->isDownloadAccess());
	}

	public function testCloseReadDownloadAcessIfNoFilesAndNoSectionsAfterPublish()
	{
		$book = factory(Book::class)
			->states('with_writer', 'private', 'lp_true')
			->create();

		$user = $book->create_user;
		$user->group->check_books = false;
		$user->group->add_book_without_check = true;
		$user->group->edit_other_user_book = false;
		$user->push();

		$this->assertEquals(0, $book->files()->count());
		$this->assertEquals(0, $book->sections()->where('type', 'section')->count());

		$response = $this->followingRedirects()
			->actingAs($user)
			->get(route('books.publish', $book))
			->assertSessionHasNoErrors()
			->assertOk()
			->assertSeeText(__('book.published'));

		$book->refresh();

		$this->assertTrue($book->isAccepted());
		$this->assertFalse($book->isReadAccess());
		$this->assertFalse($book->isDownloadAccess());
	}

	public function testSeeErrorsOnPublish()
	{
		$user = factory(User::class)
			->states('admin')
			->create();

		$book = factory(Book::class)
			->states('private')
			->create();

		$book->authors()->detach();

		$response = $this->actingAs($user)
			->get(route('books.publish', $book))
			->assertRedirect(route('books.edit', $book));
		//dump(session('errors'));
		$response->assertSessionHasErrors(['writers' => __('validation.required', ['attribute' => __('book.writers')])])
			->assertSessionHas(['try_publish' => true]);

		$response = $this->actingAs($user)
			->followingRedirects()
			->get(route('books.publish', $book))
			->assertOk()
			->assertSeeText(__('book.fix_errors_before_publish'))
			->assertSeeText(__('validation.required', ['attribute' => __('book.writers')]));
	}

	public function testDontAddForReviewFileIfAutoCreated()
	{
		$book = factory(Book::class)
			->states('with_writer', 'private', 'with_file')
			->create();

		$user = $book->create_user;
		$user->group->add_book = true;
		$user->push();

		$file = $book->files()->first();
		$file->auto_created = true;
		$file->save();

		$this->assertTrue($file->isPrivate());

		$response = $this->actingAs($user)
			->get(route('books.publish', $book))
			->assertSessionHasNoErrors()
			->assertRedirect();

		$file->refresh();

		$this->assertTrue($file->isAccepted());
		$this->assertTrue($file->isAutoCreated());
	}
}
