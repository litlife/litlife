<?php

namespace Tests\Feature\Book\BookGroup;

use App\Book;
use App\BookKeyword;
use App\BookStatus;
use App\BookVote;
use App\Comment;
use App\Jobs\Book\BookGroupJob;
use App\Jobs\Book\BookUngroupJob;
use App\Keyword;
use App\User;
use Tests\TestCase;

class BookGroupDetachTest extends TestCase
{
	public function testDetachBook()
	{
		$mainBook = Book::factory()->with_minor_book()->create();

		$minorBook = $mainBook->groupedBooks()->first();

		$this->assertEquals(1, $mainBook->editions_count);
		$this->assertEquals(1, $minorBook->editions_count);

		BookUngroupJob::dispatch($minorBook);

		$mainBook->refresh();
		$minorBook->refresh();

		$this->assertNull($mainBook->main_book_id);
		$this->assertNull($mainBook->editions_count);
		$this->assertNull($minorBook->connected_at);
		$this->assertNull($minorBook->connect_user_id);

		$this->assertNull($minorBook->main_book_id);
		$this->assertNull($minorBook->editions_count);
		$this->assertNull($minorBook->connected_at);
		$this->assertNull($minorBook->connect_user_id);
	}

	public function testDetachBookWithStatus()
	{
		$mainBook = Book::factory()->create();

		$book = Book::factory()->create();
		$book->main_book_id = $mainBook->id;
		$book->save();

		$status = BookStatus::factory()->create([
				'book_id' => $mainBook->id,
				'origin_book_id' => $book->id,
				'status' => 'readed'
			]);

		BookUngroupJob::dispatch($book);

		$status->refresh();

		$this->assertEquals($status->book_id, $book->id);
		$this->assertEquals($status->origin_book_id, $status->origin_book_id);
		$this->assertEquals($status->origin_book_id, $status->book_id);
	}

	public function testDetachBookWithVote()
	{
		$mainBook = Book::factory()->create();

		$book = Book::factory()->create();
		$book->main_book_id = $mainBook->id;
		$book->save();

		$vote = BookVote::factory()->create([
				'book_id' => $mainBook->id,
				'origin_book_id' => $book->id
			]);

		BookUngroupJob::dispatch($book);

		$vote->refresh();

		$this->assertEquals($vote->book_id, $book->id);
		$this->assertEquals($vote->origin_book_id, $vote->origin_book_id);
		$this->assertEquals($vote->origin_book_id, $vote->book_id);
	}

	public function testDetachBookWithTwoStatus()
	{
		$mainBook = Book::factory()->with_minor_book()->create();

		$minorBook = $mainBook->groupedBooks()->first();

		$user = User::factory()->create();

		$status = BookStatus::factory()->create([
				'user_id' => $user->id,
				'book_id' => $mainBook->id,
				'origin_book_id' => $minorBook->id,
				'status' => 'readed'
			]);

		$status2 = BookStatus::factory()->create([
				'user_id' => $user->id,
				'book_id' => $minorBook->id,
				'origin_book_id' => $mainBook->id,
				'status' => 'read_now'
			]);

		$status2->refresh();

		BookUngroupJob::dispatch($minorBook);

		$status->refresh();
		$status2->refresh();

		$this->assertEquals($mainBook->id, $status->book_id);
		$this->assertEquals($minorBook->id, $status->origin_book_id);

		$this->assertEquals($minorBook->id, $status2->book_id);
		$this->assertEquals($mainBook->id, $status2->origin_book_id);
	}

	public function testDetachBookWithTwoVotes()
	{
		$mainBook = Book::factory()->with_minor_book()->create();

		$minorBook = $mainBook->groupedBooks()->first();

		$user = User::factory()->create();

		$vote = BookVote::factory()->create([
				'create_user_id' => $user->id,
				'book_id' => $mainBook->id,
				'origin_book_id' => $minorBook->id,
			]);

		$vote2 = BookVote::factory()->create([
				'create_user_id' => $user->id,
				'book_id' => $minorBook->id,
				'origin_book_id' => $mainBook->id,
			]);

		BookUngroupJob::dispatch($minorBook);

		$vote->refresh();
		$vote2->refresh();

		$this->assertEquals($mainBook->id, $vote->book_id);
		$this->assertEquals($minorBook->id, $vote->origin_book_id);

		$this->assertEquals($minorBook->id, $vote2->book_id);
		$this->assertEquals($mainBook->id, $vote2->origin_book_id);
	}

	public function testDetachBookWithTwoKeywords()
	{
		$mainBook = Book::factory()->with_minor_book()->create();

		$minorBook = $mainBook->groupedBooks()->first();

		$user = User::factory()->create();

		$keyword = Keyword::factory()->create();

		$bookKeyword = BookKeyword::factory()->create([
				'keyword_id' => $keyword->id,
				'book_id' => $mainBook->id,
				'origin_book_id' => $minorBook->id,
			]);

		$bookKeyword2 = BookKeyword::factory()->create([
				'keyword_id' => $keyword->id,
				'book_id' => $minorBook->id,
				'origin_book_id' => $mainBook->id,
			]);

		BookUngroupJob::dispatch($minorBook);

		$bookKeyword->refresh();
		$bookKeyword2->refresh();

		$this->assertEquals($mainBook->id, $bookKeyword->book_id);
		$this->assertEquals($minorBook->id, $bookKeyword->origin_book_id);

		$this->assertEquals($minorBook->id, $bookKeyword2->book_id);
		$this->assertEquals($mainBook->id, $bookKeyword2->origin_book_id);
	}

	public function testDetachHttp()
	{
		$user = User::factory()->create()->fresh();
		$user->group->connect_books = true;
		$user->push();

		$mainBook = Book::factory()->with_two_minor_books()->create();

		$minorBooks = $mainBook->groupedBooks()->get();
		$minorBook = $minorBooks[0];
		$minorBook2 = $minorBooks[1];

		$response = $this->actingAs($user)
			->followingRedirects()
			->get(route('books.group.detach', ['book' => $minorBook2->id]))
			->assertOk()
			->assertSeeText(__('book_group.ungrouped'));

		$mainBook->refresh();
		$minorBook->refresh();
		$minorBook2->refresh();

		$this->assertEquals(1, $mainBook->editions_count);

		$this->assertTrue($mainBook->isInGroup());
		$this->assertTrue($mainBook->isMainInGroup());

		$this->assertTrue($minorBook->isInGroup());
		$this->assertFalse($minorBook->isMainInGroup());

		$this->assertFalse($minorBook2->isInGroup());
		$this->assertFalse($minorBook2->isMainInGroup());

		$this->assertNull($minorBook2->connect_user_id);
		$this->assertNull($minorBook2->connected_at);
	}

	public function testVoteCountersUpdated()
	{
		$user = User::factory()->create();

		$mainBook = Book::factory()->with_minor_book()->create();

		$minorBook = $mainBook->groupedBooks()->first();

		$vote = BookVote::factory()->create([
				'book_id' => $mainBook->id,
				'vote' => 7
			]);

		$vote2 = BookVote::factory()->create([
				'book_id' => $minorBook->id,
				'vote' => 5
			]);

		BookGroupJob::dispatch($mainBook, $minorBook);

		$minorBook->refresh();

		BookUngroupJob::dispatch($minorBook);

		$mainBook->refresh();
		$minorBook->refresh();

		$this->assertEquals(7, $mainBook->vote_average);
		$this->assertEquals(1, $mainBook->user_vote_count);

		$this->assertEquals(5, $minorBook->vote_average);
		$this->assertEquals(1, $minorBook->user_vote_count);

		$vote->refresh();
		$vote2->refresh();

		$this->assertEquals(1, $vote->create_user->book_rate_count);
		$this->assertEquals(1, $vote2->create_user->book_rate_count);
	}

	public function testReadStatusCountersUpdated()
	{
		$user = User::factory()->create();

		$mainBook = Book::factory()->with_minor_book()->create();

		$minorBook = $mainBook->groupedBooks()->first();

		$status = BookStatus::factory()->create([
				'book_id' => $mainBook->id,
				'status' => 'read_now'
			]);

		$status2 = BookStatus::factory()->create([
				'book_id' => $minorBook->id,
				'status' => 'read_later'
			]);

		BookGroupJob::dispatch($mainBook, $minorBook);

		$minorBook->refresh();

		BookUngroupJob::dispatch($minorBook);

		$mainBook->refresh();
		$minorBook->refresh();

		$this->assertEquals(0, $mainBook->user_read_count);
		$this->assertEquals(1, $mainBook->user_read_now_count);

		$this->assertEquals(1, $minorBook->user_read_later_count);
		$this->assertEquals(0, $minorBook->user_read_now_count);

		$status->refresh();
		$status2->refresh();

		$this->assertEquals(0, $status->user->book_read_later_count);
		$this->assertEquals(1, $status->user->book_read_now_count);

		$this->assertEquals(1, $status2->user->book_read_later_count);
		$this->assertEquals(0, $status2->user->book_read_now_count);
	}

	public function testUpdateCountersImmediatelyTrue()
	{
		$mainBook = Book::factory()->with_minor_book()->create();

		$minorBook = $mainBook->groupedBooks()->first();

		$status = BookStatus::factory()->create([
				'book_id' => $mainBook->id,
				'status' => 'read_now'
			]);

		$status2 = BookStatus::factory()->create([
				'book_id' => $minorBook->id,
				'status' => 'read_later'
			]);

		BookUngroupJob::dispatch($minorBook, false);

		$status->refresh();
		$status2->refresh();

		$this->assertTrue($status->user->refresh_counters);
		$this->assertTrue($status2->user->refresh_counters);
	}

	public function testUpdateCountersImmediatelyFalse()
	{
		$mainBook = Book::factory()->with_minor_book()->create();

		$minorBook = $mainBook->groupedBooks()->first();

		$status = BookStatus::factory()->create([
				'book_id' => $mainBook->id,
				'status' => 'read_now'
			]);

		$status2 = BookStatus::factory()->create([
				'book_id' => $minorBook->id,
				'status' => 'read_later'
			]);

		BookUngroupJob::dispatch($minorBook);

		$status->refresh();
		$status2->refresh();

		$this->assertNull($status->user->refresh_counters);
		$this->assertNull($status2->user->refresh_counters);
	}

	public function testBookWithTwoComments()
	{
		$mainBook = Book::factory()->create();

		$minorBook = Book::factory()->create();

		$comment = Comment::factory()->book()->create();

		$comment2 = Comment::factory()->book()->create();

		BookGroupJob::dispatch($mainBook, $minorBook);
		BookUngroupJob::dispatch($minorBook);

		$comment->refresh();
		$comment2->refresh();
		$mainBook->refresh();
		$minorBook->refresh();

		$this->assertEquals($mainBook->id, $comment->commentable_id);
		$this->assertEquals($mainBook->id, $comment->origin_commentable_id);

		$this->assertEquals($minorBook->id, $comment2->commentable_id);
		$this->assertEquals($minorBook->id, $comment2->origin_commentable_id);

		$this->assertEquals(1, $mainBook->comment_count);
		$this->assertEquals(1, $minorBook->comment_count);
	}

	public function testEditionsCount()
	{
		$mainBook = Book::factory()->create()->fresh();

		$minorBook = Book::factory()->create()->fresh();

		BookGroupJob::dispatch($mainBook, $minorBook);

		$mainBook->refresh();
		$minorBook->refresh();

		BookUngroupJob::dispatch($minorBook);

		$mainBook->refresh();
		$minorBook->refresh();

		$this->assertNull($mainBook->main_book_id);
		$this->assertNull($mainBook->editions_count);

		$this->assertNull($minorBook->main_book_id);
		$this->assertNull($minorBook->editions_count);
	}

	public function testTwoMinorBooks()
	{
		$mainBook = Book::factory()->create();
		$minorBook = Book::factory()->create();
		$minorBook2 = Book::factory()->create();

		BookGroupJob::dispatch($mainBook, $minorBook);
		BookGroupJob::dispatch($mainBook, $minorBook2);

		$mainBook->refresh();
		$minorBook->refresh();
		$minorBook2->refresh();

		$this->assertTrue($mainBook->isMainInGroup());
		$this->assertTrue($minorBook->isInGroup());
		$this->assertTrue($minorBook2->isInGroup());

		BookUngroupJob::dispatch($minorBook2);

		$mainBook->refresh();
		$minorBook->refresh();
		$minorBook2->refresh();

		$this->assertNull($mainBook->main_book_id);
		$this->assertEquals(1, $mainBook->editions_count);

		$this->assertEquals($mainBook->id, $minorBook->main_book_id);
		$this->assertEquals(1, $minorBook->editions_count);

		$this->assertNull($minorBook2->main_book_id);
		$this->assertNull($minorBook2->editions_count);
	}

	public function testDetachOnRestoreMinorBookIfMainBookIsDeleted()
	{
		$mainBook = Book::factory()->create();
		$minorBook = Book::factory()->create();

		BookGroupJob::dispatch($mainBook, $minorBook);

		$minorBook->delete();
		$mainBook->delete();

		$minorBook->refresh();
		$mainBook->refresh();

		$minorBook->restore();

		$minorBook->refresh();
		$mainBook->refresh();

		$this->assertFalse($minorBook->trashed());
		$this->assertFalse($minorBook->isInGroup());
		$this->assertNull($minorBook->main_book_id);
		$this->assertNull($minorBook->editions_count);
	}

	public function testDetachOnRestoreMinorBookIfMainBookIsForceDeleted()
	{
		$mainBook = Book::factory()->create();
		$minorBook = Book::factory()->create();

		BookGroupJob::dispatch($mainBook, $minorBook);

		$minorBook->delete();
		$mainBook->forceDelete();

		$minorBook->refresh();
		$mainBook->refresh();

		$minorBook->restore();

		$minorBook->refresh();
		$mainBook->refresh();

		$this->assertFalse($minorBook->trashed());
		$this->assertFalse($minorBook->isInGroup());
		$this->assertNull($minorBook->main_book_id);
		$this->assertNull($minorBook->editions_count);
	}
}