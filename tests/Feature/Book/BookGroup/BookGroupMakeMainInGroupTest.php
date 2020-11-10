<?php

namespace Tests\Feature\Book\BookGroup;

use App\Book;
use App\BookStatus;
use App\BookVote;
use App\Comment;
use App\Jobs\Book\BookGroupJob;
use App\Jobs\Book\BookMakeMainInGroupJob;
use App\User;
use Tests\TestCase;

class BookGroupMakeMainInGroupTest extends TestCase
{
	public function testMakeMainInGroup()
	{
		$book = Book::factory()->with_minor_book()->create();

		$minorBook = $book->groupedBooks()->first();

		$book->updateEditionsCount();

		BookMakeMainInGroupJob::dispatch($minorBook);

		$book->refresh();
		$minorBook->refresh();

		$this->assertNull($minorBook->main_book_id);
		$this->assertNotNull($book->main_book_id);
		$this->assertEquals($minorBook->id, $book->main_book_id);
		$this->assertEquals(1, $book->editions_count);
		$this->assertEquals(1, $minorBook->editions_count);
		$this->assertFalse($book->isMainInGroup());
		$this->assertTrue($minorBook->isMainInGroup());
	}

	public function testMakeMainInGroupWithStatus()
	{
		$mainBook = Book::factory()->create();

		$minorBook = Book::factory()->create(['main_book_id' => $mainBook->id]);

		$status = BookStatus::factory()->create([
				'book_id' => $mainBook->id,
				'origin_book_id' => $minorBook->id
			]);

		BookMakeMainInGroupJob::dispatch($minorBook);

		$status->refresh();

		$this->assertEquals($status->book_id, $minorBook->id);
		$this->assertEquals($status->origin_book_id, $minorBook->id);
	}

	public function testMakeMainInGroupWithBookVote()
	{
		$mainBook = Book::factory()->create();

		$minorBook = Book::factory()->create(['main_book_id' => $mainBook->id]);

		$vote = BookVote::factory()->create([
				'book_id' => $mainBook->id,
				'origin_book_id' => $minorBook->id,
			]);

		BookMakeMainInGroupJob::dispatch($minorBook);

		$vote->refresh();

		$this->assertEquals($vote->book_id, $minorBook->id);
		$this->assertEquals($vote->origin_book_id, $minorBook->id);
	}

	public function testIsMainInGroup()
	{
		$mainBook = Book::factory()->with_minor_book()->create();

		$minorBook = $mainBook->groupedBooks()->first();

		$this->assertTrue($mainBook->isMainInGroup());
		$this->assertFalse($minorBook->isMainInGroup());

		$this->assertFalse($mainBook->isNotMainInGroup());
		$this->assertTrue($minorBook->isNotMainInGroup());
	}

	public function testMainGroupRelation()
	{
		$mainBook = Book::factory()->with_minor_book()->create();

		$minorBook = $mainBook->groupedBooks()->first();

		$this->assertTrue($mainBook->is($minorBook->mainBook));
	}

	public function testBookWithStatusAndSameUser()
	{
		$user = User::factory()->create();

		$mainBook = Book::factory()->with_minor_book()->create();

		$minorBook = $mainBook->groupedBooks()->first();

		$status = BookStatus::factory()->create(['status' => 'readed',
				'book_id' => $mainBook->id,
				'user_id' => $user->id]);

		$status2 = BookStatus::factory()->create(['status' => 'read_later',
				'book_id' => $minorBook->id,
				'user_id' => $user->id]);

		BookMakeMainInGroupJob::dispatch($minorBook);

		$status->refresh();
		$status2->refresh();

		$this->assertEquals($mainBook->id, $status->book_id);
		$this->assertEquals($minorBook->id, $status2->book_id);
	}

	public function testAttachBookWithVoteAndSameUser()
	{
		$user = User::factory()->create();

		$mainBook = Book::factory()->with_minor_book()->create();

		$minorBook = $mainBook->groupedBooks()->first();

		$vote = BookVote::factory()->create([
				'book_id' => $mainBook->id,
				'create_user_id' => $user->id
			]);

		$vote2 = BookVote::factory()->create([
				'book_id' => $minorBook->id,
				'create_user_id' => $user->id
			]);

		BookMakeMainInGroupJob::dispatch($minorBook);

		$vote->refresh();
		$vote2->refresh();

		$this->assertEquals($mainBook->id, $vote->book_id);
		$this->assertEquals($minorBook->id, $vote2->book_id);
	}

	public function testTwoMinorBooks()
	{
		$user = User::factory()->create();

		$mainBook = Book::factory()->with_two_minor_books()->create();

		$minorBooks = $mainBook->groupedBooks()->get();
		$minorBook = $minorBooks[0];
		$minorBook2 = $minorBooks[1];

		BookMakeMainInGroupJob::dispatch($minorBook2);

		$mainBook->refresh();
		$minorBook->refresh();
		$minorBook2->refresh();

		$this->assertEquals($minorBook2->id, $mainBook->main_book_id);
		$this->assertEquals($minorBook2->id, $minorBook->main_book_id);
		$this->assertEquals(null, $minorBook2->main_book_id);

		$this->assertTrue($mainBook->isInGroup());
		$this->assertTrue($minorBook->isInGroup());
		$this->assertTrue($minorBook2->isInGroup());

		$this->assertFalse($mainBook->isMainInGroup());
		$this->assertFalse($minorBook->isMainInGroup());
		$this->assertTrue($minorBook2->isMainInGroup());
	}


	public function testMakeMainInGroupHttp()
	{
		$user = User::factory()->create()->fresh();
		$user->group->connect_books = true;
		$user->push();

		$mainBook = Book::factory()->with_minor_book()->create();

		$minorBook = $mainBook->groupedBooks()->first();

		$response = $this->actingAs($user)
			->get(route('books.group.make_main_in_group', ['book' => $minorBook->id]))
			->assertSessionHasNoErrors()
			->assertRedirect();

		$minorBook->refresh();
		$mainBook->refresh();

		$this->assertTrue($minorBook->isMainInGroup());
		$this->assertFalse($mainBook->isMainInGroup());
	}

	public function testChangeMainInGroupHttp()
	{
		$user = User::factory()->create()->fresh();
		$user->group->connect_books = true;
		$user->push();

		$mainBook = Book::factory()->with_two_minor_books()->create();

		$minorBooks = $mainBook->groupedBooks()->get();
		$minorBook = $minorBooks[0];
		$minorBook2 = $minorBooks[1];

		$response = $this->actingAs($user)
			->get(route('books.group.make_main_in_group', ['book' => $minorBook2->id]))
			->assertSessionHasNoErrors()
			->assertRedirect();

		$mainBook->refresh();
		$minorBook->refresh();
		$minorBook2->refresh();

		$this->assertFalse($mainBook->isMainInGroup());
		$this->assertFalse($minorBook->isMainInGroup());
		$this->assertTrue($minorBook2->isMainInGroup());
	}

	public function testBookWithTwoComments()
	{
		$mainBook = Book::factory()->create();

		$minorBook = Book::factory()->create();

		$comment = Comment::factory()->book()->create();

		$comment2 = Comment::factory()->book()->create();

		BookGroupJob::dispatch($mainBook, $minorBook);
		BookMakeMainInGroupJob::dispatch($minorBook);

		$comment->refresh();
		$comment2->refresh();

		$this->assertEquals($minorBook->id, $comment->commentable_id);
		$this->assertEquals($mainBook->id, $comment->origin_commentable_id);

		$this->assertEquals($minorBook->id, $comment2->commentable_id);
		$this->assertEquals($minorBook->id, $comment2->origin_commentable_id);
	}
}