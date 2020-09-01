<?php

namespace Tests\Feature\Book;

use App\Attachment;
use App\Author;
use App\Book;
use App\BookFile;
use App\BookKeyword;
use App\Notifications\BookDeletedNotification;
use App\Section;
use App\User;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class BookDeleteTest extends TestCase
{
	public function testCantDeleteIfBookPurchased()
	{
		$user = factory(User::class)->create();
		$user->group->delete_other_user_book = true;
		$user->push();

		$book = factory(Book::class)->create();
		$book->bought_times_count = 0;
		$book->push();

		$this->assertTrue($user->can('delete', $book));

		$book = factory(Book::class)->create();
		$book->bought_times_count = 1;
		$book->push();

		$this->assertFalse($user->can('delete', $book));
	}

	public function testViewBookDeletedHttpCode()
	{
		$book = factory(Book::class)
			->create();

		$book->delete();

		$this->get(route('books.show', $book))
			->assertNotFound()
			->assertSeeText(__('Book was deleted'));
	}

	public function testDeleteFormIsOk()
	{
		$admin = factory(User::class)->states('administrator')->create();

		$book = factory(Book::class)->create();

		$this->actingAs($admin)
			->get(route('books.delete.form', $book))
			->assertOk()
			->assertSee('name="reason_for_deleting"', false);
	}

	public function testDeleteHttp()
	{
		config(['activitylog.enabled' => true]);

		Notification::fake();

		$admin = factory(User::class)->states('administrator')->create();

		$book = factory(Book::class)->create();

		$reason = $this->faker->realText(100);

		$this->actingAs($admin)
			->get(route('books.delete', [
				'book' => $book,
				'reason_for_deleting' => $reason
			]))
			->assertRedirect(route('books.show', $book));

		$book->refresh();

		$this->assertSoftDeleted($book);

		$activity = $book->activities()->first();

		$this->assertEquals(1, $book->activities()->count());
		$this->assertEquals('deleted', $activity->description);
		$this->assertEquals($admin->id, $activity->causer_id);
		$this->assertEquals('user', $activity->causer_type);
		$this->assertEquals($reason, $activity->getExtraProperty('reason'));

		$book->forceDelete();

		$this->assertEquals(1, $book->activities()->count());

		Notification::assertSentTo(
			$book->create_user,
			BookDeletedNotification::class,
			function ($notification, $channels) use ($book, $reason) {

				$this->assertContains('database', $channels);

				$array = $notification->toArray($book, $reason);

				$this->assertEquals(__('notification.book_deleted.subject'), $array['title']);

				$this->assertEquals(__('notification.book_deleted.line', [
						'book_title' => $book->title,
						'writers_names' => optional($book->writers()->first())->name
					]) . ' ' . __('notification.book_deleted.reason', ['reason' => $reason]),
					$array['description']);

				$this->assertEquals(route('books.show', $book), $array['url']);

				return $notification->book->id == $book->id;
			}
		);
	}

	public function testRestoreHttp()
	{
		config(['activitylog.enabled' => true]);

		$admin = factory(User::class)->states('admin')->create();

		$book = factory(Book::class)->states('soft_deleted')->create();

		$this->assertTrue($book->trashed());

		$this->actingAs($admin)
			->get(route('books.restore', $book))
			->assertRedirect();

		$book->refresh();

		$this->assertFalse($book->trashed());

		$activity = $book->activities()->first();

		$this->assertEquals(1, $book->activities()->count());
		$this->assertEquals('restored', $activity->description);
		$this->assertEquals($admin->id, $activity->causer_id);
		$this->assertEquals('user', $activity->causer_type);
	}

	public function testPrivateBookRestorePolicy()
	{
		$book = factory(Book::class)
			->states('private')
			->create();

		$user = $book->create_user;

		$section = factory(Section::class)->create(['book_id' => $book->id]);
		$section->delete();
		$this->assertTrue($user->can('restore', $section));

		$attachment = factory(Attachment::class)->create(['book_id' => $book->id]);
		$attachment->delete();
		$this->assertTrue($user->can('restore', $attachment));

		$file = factory(BookFile::class)->states('txt')->create(['book_id' => $book->id]);
		$file->delete();
		$this->assertTrue($user->can('restore', $file));

		$book_keyword = factory(BookKeyword::class)->create(['book_id' => $book->id]);
		$book_keyword->delete();
		$this->assertTrue($user->can('restore', $book_keyword));
	}

	public function testForceDeleteWithAttachment()
	{
		$book = factory(Book::class)->states('with_cover')->create();

		$book->forceDelete();

		$this->assertNull(Book::find($book->id));
	}

	public function testBookDeleteNotificaionWithoutReason()
	{
		$book = factory(Book::class)
			->create();

		$user = factory(User::class)
			->create();

		$notification = new BookDeletedNotification($book, '');

		$array = $notification->toArray($user);

		$this->assertEquals(__('notification.book_deleted.subject'), $array['title']);
		$this->assertEquals(__('notification.book_deleted.line', [
			'book_title' => $book->title,
			'writers_names' => optional($book->writers()->first())->name,
		]), $array['description']);
		$this->assertEquals(route('books.show', $book), $array['url']);
	}

	public function testCanDeleteBookIfNoRatingAndNoComments()
	{
		$author = factory(Author::class)
			->states('with_author_manager', 'with_book')
			->create();

		$user = $author->managers->first()->user;
		$book = $author->books->first();

		$this->assertTrue($user->can('delete', $book));
	}
	/*
		public function testCantDeleteBookIfHasComment()
		{
			$author = factory(Author::class)
				->states('with_author_manager', 'with_book')
				->create();

			$user = $author->managers->first()->user;
			$book = $author->books->first();
			$book->comment_count = 1;
			$book->save();

			$this->assertFalse($user->can('delete', $book));
		}

		public function testCantDeleteBookIfHasVotes()
		{
			$author = factory(Author::class)
				->states('with_author_manager', 'with_book')
				->create();

			$user = $author->managers->first()->user;
			$book = $author->books->first();
			$book->user_vote_count = 1;
			$book->save();

			$this->assertFalse($user->can('delete', $book));
		}
		*/
}
