<?php

namespace Tests\Feature\Book\BookGroup;

use App\Book;
use App\BookGroup;
use App\BookStatus;
use App\BookVote;
use App\Comment;
use App\Jobs\Book\BookGroupJob;
use App\Jobs\Book\UpdateBookRating;
use App\Keyword;
use App\User;
use Tests\TestCase;

class BookGroupTest extends TestCase
{
	public function testIndex()
	{
		$user = factory(User::class)->create()->fresh();
		$user->group->connect_books = true;
		$user->push();

		$book = factory(Book::class)->create();
		$book->statusAccepted();
		$book->push();

		$response = $this->actingAs($user)
			->get(route('books.editions.edit', ['book' => $book]))
			->assertSessionHasNoErrors()
			->assertOk();
	}

	public function testNotDeleteMainBookVoteFromSameGroup()
	{
		$user = factory(User::class)->create();
		$user->group->vote_for_book = true;
		$user->push();

		$mainBook = factory(Book::class)
			->states('with_minor_book')
			->create();

		$minorBook = $mainBook->groupedBooks()->first();

		$vote = factory(BookVote::class)
			->create([
				'book_id' => $mainBook->id,
				'create_user_id' => $user->id,
				'vote' => 3
			]);

		$other_user_vote = factory(BookVote::class)
			->create(['book_id' => $mainBook->id, 'vote' => 6]);

		$vote2 = factory(BookVote::class)
			->create([
				'book_id' => $minorBook->id,
				'create_user_id' => $user->id,
				'vote' => 5
			]);

		$response = $this->actingAs($user)
			->get(route('books.vote', [
				'book' => $minorBook,
				'vote' => 6
			]))
			->assertRedirect();

		$vote->refresh();
		$vote2->refresh();

		$this->assertEquals(6, $vote->vote);
		$this->assertEquals($mainBook->id, $vote->book_id);
		$this->assertEquals($vote2->book_id, $vote2->origin_book_id);
		$this->assertFalse($vote->is($vote2));
		$this->assertFalse($vote->trashed());
		$this->assertFalse($vote2->trashed());
		$this->assertFalse($other_user_vote->trashed());
	}

	public function testDeleteMinorBookVoteFromSameGroup()
	{
		$user = factory(User::class)->create();
		$user->group->vote_for_book = true;
		$user->push();

		$mainBook = factory(Book::class)
			->states('with_minor_book')
			->create();

		$vote = factory(BookVote::class)
			->create([
				'book_id' => $mainBook->id,
				'create_user_id' => $user->id,
				'vote' => 3
			]);

		$other_user_vote = factory(BookVote::class)
			->create(['book_id' => $mainBook->id, 'vote' => 6]);

		$minorBook = $mainBook->groupedBooks()->first();

		$vote2 = factory(BookVote::class)
			->create([
				'book_id' => $minorBook->id,
				'create_user_id' => $user->id,
				'vote' => 5
			]);

		$response = $this->actingAs($user)
			->get(route('books.vote', [
				'book' => $mainBook,
				'vote' => 6
			]))
			->assertRedirect();

		$vote->refresh();
		$vote2->refresh();

		$this->assertEquals(6, $vote->vote);
		$this->assertFalse($vote->is($vote2));
		$this->assertFalse($vote->trashed());
		$this->assertFalse($vote2->trashed());
		$this->assertFalse($other_user_vote->trashed());
	}

	public function testUpdateBookRating()
	{
		$mainBook = factory(Book::class)
			->create();

		$minorBook = factory(Book::class)
			->create();

		$vote = factory(BookVote::class)->create(['book_id' => $mainBook->id, 'vote' => 7]);
		$vote2 = factory(BookVote::class)->create(['book_id' => $minorBook->id, 'vote' => 3]);

		BookGroupJob::dispatch($mainBook, $minorBook);

		UpdateBookRating::dispatch($mainBook);

		$mainBook->refresh();
		$minorBook->refresh();

		$this->assertEquals(5, $mainBook->vote_average);
		$this->assertEquals(5, $minorBook->vote_average);

		$this->assertEquals(2, $mainBook->user_vote_count);
		$this->assertEquals(2, $minorBook->user_vote_count);

		$this->assertEquals(2, $minorBook->average_rating_for_period->day_votes_count);
		$this->assertEquals(2, $minorBook->average_rating_for_period->week_votes_count);
		$this->assertEquals(2, $minorBook->average_rating_for_period->month_votes_count);
		$this->assertEquals(2, $minorBook->average_rating_for_period->quarter_votes_count);
		$this->assertEquals(2, $minorBook->average_rating_for_period->year_votes_count);

		$this->assertEquals($mainBook->in_rating, $minorBook->in_rating);
		$this->assertEquals($mainBook->rate_info, $minorBook->rate_info);

		$this->assertEquals(1, $minorBook->rate_info[7]['count']);
		$this->assertEquals(1, $minorBook->rate_info[3]['count']);

		$this->assertEquals(1, $mainBook->rate_info[7]['count']);
		$this->assertEquals(1, $mainBook->rate_info[3]['count']);

		$this->assertEquals($mainBook->male_vote_count, $minorBook->male_vote_count);
		$this->assertEquals($mainBook->female_vote_count, $minorBook->female_vote_count);
		$this->assertEquals($mainBook->male_vote_percent, $minorBook->male_vote_percent);
		$this->assertEquals($mainBook->refresh_rating, $minorBook->refresh_rating);

		$this->assertEquals($mainBook->average_rating_for_period->day_vote_average, $minorBook->average_rating_for_period->day_vote_average);
		$this->assertEquals($mainBook->average_rating_for_period->day_votes_count, $minorBook->average_rating_for_period->day_votes_count);
		$this->assertEquals($mainBook->average_rating_for_period->day_rating, $minorBook->average_rating_for_period->day_rating);

		$this->assertEquals($mainBook->average_rating_for_period->week_vote_average, $minorBook->average_rating_for_period->week_vote_average);
		$this->assertEquals($mainBook->average_rating_for_period->week_votes_count, $minorBook->average_rating_for_period->week_votes_count);
		$this->assertEquals($mainBook->average_rating_for_period->week_rating, $minorBook->average_rating_for_period->week_rating);

		$this->assertEquals($mainBook->average_rating_for_period->month_vote_average, $minorBook->average_rating_for_period->month_vote_average);
		$this->assertEquals($mainBook->average_rating_for_period->month_votes_count, $minorBook->average_rating_for_period->month_votes_count);
		$this->assertEquals($mainBook->average_rating_for_period->month_rating, $minorBook->average_rating_for_period->month_rating);

		$this->assertEquals($mainBook->average_rating_for_period->quarter_vote_average, $minorBook->average_rating_for_period->quarter_vote_average);
		$this->assertEquals($mainBook->average_rating_for_period->quarter_votes_count, $minorBook->average_rating_for_period->quarter_votes_count);
		$this->assertEquals($mainBook->average_rating_for_period->quarter_rating, $minorBook->average_rating_for_period->quarter_rating);

		$this->assertEquals($mainBook->average_rating_for_period->year_vote_average, $minorBook->average_rating_for_period->year_vote_average);
		$this->assertEquals($mainBook->average_rating_for_period->year_votes_count, $minorBook->average_rating_for_period->year_votes_count);
		$this->assertEquals($mainBook->average_rating_for_period->year_rating, $minorBook->average_rating_for_period->year_rating);
	}

	public function testUpdateBookRatingIfMainBookAverageRatingForPeriodNotExists()
	{
		$mainBook = factory(Book::class)
			->create();

		$minorBook = factory(Book::class)
			->create();

		$vote = factory(BookVote::class)->create(['book_id' => $mainBook->id, 'vote' => 7]);
		$vote2 = factory(BookVote::class)->create(['book_id' => $minorBook->id, 'vote' => 3]);

		BookGroupJob::dispatch($mainBook, $minorBook);

		$mainBook->average_rating_for_period->forceDelete();

		UpdateBookRating::dispatch($minorBook);

		$mainBook->refresh();
		$minorBook->refresh();

		$this->assertEquals(5, $mainBook->vote_average);
		$this->assertEquals(5, $minorBook->vote_average);
		/*
				$this->assertEquals(2, $mainBook->user_vote_count);
				$this->assertEquals(2, $minorBook->user_vote_count);

				$this->assertEquals(2, $minorBook->average_rating_for_period->day_votes_count);
				$this->assertEquals(2, $minorBook->average_rating_for_period->week_votes_count);
				$this->assertEquals(2, $minorBook->average_rating_for_period->month_votes_count);
				$this->assertEquals(2, $minorBook->average_rating_for_period->quarter_votes_count);
				$this->assertEquals(2, $minorBook->average_rating_for_period->year_votes_count);

				$this->assertEquals($mainBook->in_rating, $minorBook->in_rating);
				$this->assertEquals($mainBook->rate_info, $minorBook->rate_info);

				$this->assertEquals(1, $minorBook->rate_info[7]['count']);
				$this->assertEquals(1, $minorBook->rate_info[3]['count']);

				$this->assertEquals(1, $mainBook->rate_info[7]['count']);
				$this->assertEquals(1, $mainBook->rate_info[3]['count']);

				$this->assertEquals($mainBook->male_vote_count, $minorBook->male_vote_count);
				$this->assertEquals($mainBook->female_vote_count, $minorBook->female_vote_count);
				$this->assertEquals($mainBook->male_vote_percent, $minorBook->male_vote_percent);
				$this->assertEquals($mainBook->refresh_rating, $minorBook->refresh_rating);

				$this->assertEquals($mainBook->average_rating_for_period->day_vote_average, $minorBook->average_rating_for_period->day_vote_average);
				$this->assertEquals($mainBook->average_rating_for_period->day_votes_count, $minorBook->average_rating_for_period->day_votes_count);
				$this->assertEquals($mainBook->average_rating_for_period->day_rating, $minorBook->average_rating_for_period->day_rating);

				$this->assertEquals($mainBook->average_rating_for_period->week_vote_average, $minorBook->average_rating_for_period->week_vote_average);
				$this->assertEquals($mainBook->average_rating_for_period->week_votes_count, $minorBook->average_rating_for_period->week_votes_count);
				$this->assertEquals($mainBook->average_rating_for_period->week_rating, $minorBook->average_rating_for_period->week_rating);

				$this->assertEquals($mainBook->average_rating_for_period->month_vote_average, $minorBook->average_rating_for_period->month_vote_average);
				$this->assertEquals($mainBook->average_rating_for_period->month_votes_count, $minorBook->average_rating_for_period->month_votes_count);
				$this->assertEquals($mainBook->average_rating_for_period->month_rating, $minorBook->average_rating_for_period->month_rating);

				$this->assertEquals($mainBook->average_rating_for_period->quarter_vote_average, $minorBook->average_rating_for_period->quarter_vote_average);
				$this->assertEquals($mainBook->average_rating_for_period->quarter_votes_count, $minorBook->average_rating_for_period->quarter_votes_count);
				$this->assertEquals($mainBook->average_rating_for_period->quarter_rating, $minorBook->average_rating_for_period->quarter_rating);

				$this->assertEquals($mainBook->average_rating_for_period->year_vote_average, $minorBook->average_rating_for_period->year_vote_average);
				$this->assertEquals($mainBook->average_rating_for_period->year_votes_count, $minorBook->average_rating_for_period->year_votes_count);
				$this->assertEquals($mainBook->average_rating_for_period->year_rating, $minorBook->average_rating_for_period->year_rating);
			*/
	}

	public function testSeeUserVoted()
	{
		$user = factory(User::class)->create();

		$vote = factory(BookVote::class)->create(['vote' => 7]);
		$vote2 = factory(BookVote::class)->create(['vote' => 3]);

		$book = $vote->book;
		$book2 = $vote2->book;

		BookGroupJob::dispatch($book, $book2);

		$this->actingAs($user)
			->get(route('books.votes', ['book' => $book]))
			->assertOk()
			->assertSeeText($vote->create_user->nick)
			->assertSeeText($vote2->create_user->nick);

		$this->actingAs($user)
			->get(route('books.votes', ['book' => $book2]))
			->assertOk()
			->assertSeeText($vote->create_user->nick)
			->assertSeeText($vote2->create_user->nick);
	}

	public function testBookVotesRelation()
	{
		$user = factory(User::class)->create();

		$group = factory(BookGroup::class)->create();

		$vote = factory(BookVote::class)->create(['vote' => 7]);
		$vote2 = factory(BookVote::class)->create(['vote' => 3]);

		$book = $vote->book;
		$book2 = $vote2->book;

		$group->addBook($book);
		$group->addBook($book2);

		$votes = $group->bookVotes()->orderBy('vote', 'desc')->get();

		$this->assertEquals(2, $votes->count());
		$this->assertEquals(7, $votes[0]->vote);
		$this->assertEquals(3, $votes[1]->vote);
		$this->assertTrue($vote->is($votes[0]));
		$this->assertTrue($vote2->is($votes[1]));
	}

	public function testNotDeleteAllOtherReadStatusForBooksFromSameGroup()
	{
		$user = factory(User::class)->create();
		$user->group->vote_for_book = true;
		$user->push();

		$mainBook = factory(Book::class)
			->states('with_minor_book')
			->create();

		$minorBook = $mainBook->groupedBooks()->first();

		$status = factory(BookStatus::class)
			->create([
				'book_id' => $mainBook->id,
				'user_id' => $user->id,
				'status' => 'readed'
			]);

		//$other_user_status = factory(BookStatus::class)->create(['book_id' => $mainBook->id, 'status' => 'read_later']);

		$status2 = factory(BookStatus::class)
			->create([
				'book_id' => $minorBook->id,
				'user_id' => $user->id,
				'status' => 'not_read'
			]);

		$response = $this->actingAs($user)
			->get(route('books.read_status.store', [
				'book' => $minorBook,
				'code' => 'read_now'
			]))
			->assertRedirect();

		$status->refresh();
		$status2->refresh();
		//$this->assertDatabaseMissing('book_statuses', ['id' => $status2->id]);

		$this->assertEquals(1, $mainBook->statuses()->where('user_id', $user->id)->count());

		$this->assertEquals('read_now', $status->status);
		$this->assertEquals('not_read', $status2->status);
		$this->assertEquals($mainBook->id, $status->book_id);
		$this->assertEquals($minorBook->id, $status->origin_book_id);
	}

	public function testIsInGroup()
	{
		$mainBook = factory(Book::class)
			->states('with_minor_book')
			->create();

		$minorBook = $mainBook->groupedBooks()->first();

		$this->assertTrue($mainBook->isInGroup());
		$this->assertTrue($minorBook->isInGroup());
	}

	public function testVoteForMinorBook()
	{
		$user = factory(User::class)->create();
		$user->group->vote_for_book = true;
		$user->push();

		$mainBook = factory(Book::class)
			->states('with_minor_book')
			->create();

		$minorBook = $mainBook->groupedBooks()->first();

		$response = $this->actingAs($user)
			->get(route('books.vote', [
				'book' => $minorBook->id,
				'vote' => 5
			]))
			->assertRedirect();

		$vote = $mainBook->votes()->first();

		$this->assertNotNull($vote);

		$this->assertEquals($mainBook->id, $vote->book_id);
		$this->assertEquals($minorBook->id, $vote->origin_book_id);
		$this->assertEquals(5, $vote->vote);
	}

	public function testUpdateVoteForMinorBook()
	{
		$user = factory(User::class)->create();
		$user->group->vote_for_book = true;
		$user->push();

		$mainBook = factory(Book::class)
			->states('with_minor_book')
			->create();

		$minorBook = $mainBook->groupedBooks()->first();

		$vote = factory(BookVote::class)
			->create([
				'create_user_id' => $user->id,
				'book_id' => $mainBook->id,
				'vote' => 2
			]);

		$response = $this->actingAs($user)
			->get(route('books.vote', [
				'book' => $minorBook->id,
				'vote' => 7
			]))
			->assertRedirect();

		$vote->refresh();

		$this->assertEquals(7, $vote->vote);
		$this->assertEquals($minorBook->id, $vote->origin_book_id);
		$this->assertEquals($mainBook->id, $vote->book_id);
	}

	public function testBookStatusForMinorBook()
	{
		$user = factory(User::class)
			->create();

		$mainBook = factory(Book::class)
			->states('with_minor_book')
			->create();

		$minorBook = $mainBook->groupedBooks()->first();

		$response = $this->actingAs($user)
			->get(route('books.read_status.store', [
				'book' => $minorBook,
				'code' => 'read_later'
			]))
			->assertRedirect();

		$status = $mainBook->users_read_statuses()->first();

		$this->assertEquals($mainBook->id, $status->book_id);
		$this->assertEquals($minorBook->id, $status->origin_book_id);

		$this->assertEquals(0, $minorBook->users_read_statuses()->count());
		$this->assertEquals(1, $mainBook->users_read_statuses()->count());

		$this->assertEquals('read_later', $status->status);
	}

	public function testUpdateBookStatusForMinorBook()
	{
		$user = factory(User::class)
			->create();

		$mainBook = factory(Book::class)
			->states('with_minor_book')
			->create();

		$minorBook = $mainBook->groupedBooks()
			->first();

		$status = factory(BookStatus::class)
			->create([
				'user_id' => $user->id,
				'book_id' => $mainBook->id,
				'status' => 'read_now'
			]);

		$response = $this->actingAs($user)
			->get(route('books.read_status.store', [
				'book' => $minorBook,
				'code' => 'read_later'
			]))
			->assertRedirect();

		$status->refresh();

		$this->assertEquals('read_later', $status->status);
		$this->assertEquals($mainBook->id, $status->book_id);
		$this->assertEquals($minorBook->id, $status->origin_book_id);
	}

	public function testGroupTwoBooks()
	{
		$user = factory(User::class)
			->states('admin')
			->create();

		$mainBook = factory(Book::class)
			->create();

		$minorBook = factory(Book::class)
			->create();

		$minorBook2 = factory(Book::class)
			->create();

		$response = $this->actingAs($user)
			->post(route('books.group.attach', ['book' => $mainBook]),
				[
					'edition_id' => $minorBook->id
				])
			->assertSessionHasNoErrors()
			->assertRedirect();

		$minorBook->refresh();
		$mainBook->refresh();

		$this->assertTrue($minorBook->isInGroup());
		$this->assertEquals($mainBook->id, $minorBook->main_book_id);
		$this->assertEquals(1, $mainBook->editions_count);

		$response = $this->actingAs($user)
			->post(route('books.group.attach', ['book' => $mainBook]),
				[
					'edition_id' => $minorBook2->id
				])
			->assertSessionHasNoErrors()
			->assertRedirect();

		$minorBook2->refresh();
		$mainBook->refresh();

		$this->assertTrue($minorBook2->isInGroup());
		$this->assertEquals($mainBook->id, $minorBook2->main_book_id);
		$this->assertEquals(2, $mainBook->editions_count);
	}

	public function testBookThatIsAttachedToMustBeTheMainOne()
	{
		$user = factory(User::class)
			->states('admin')
			->create();

		$mainBook = factory(Book::class)
			->states('with_minor_book')
			->create();

		$minorBook = $mainBook->groupedBooks()->first();

		$book = factory(Book::class)
			->create();

		$response = $this->actingAs($user)
			->post(route('books.group.attach', ['book' => $minorBook]),
				[
					'edition_id' => $book->id
				])
			->assertSessionHasErrors(['edition_id' => __('book_group.the_book_that_is_attached_to_must_be_the_main_one')])
			->assertRedirect();
	}

	public function testIsAttachedToBook()
	{
		$mainBook = factory(Book::class)->create();

		$minorBook = factory(Book::class)->create();

		$this->assertFalse($mainBook->isAttachedToBook($minorBook));
		$this->assertFalse($minorBook->isAttachedToBook($mainBook));

		BookGroupJob::dispatch($mainBook, $minorBook);

		$mainBook->refresh();
		$minorBook->refresh();

		$this->assertFalse($mainBook->isAttachedToBook($minorBook));
		$this->assertTrue($minorBook->isAttachedToBook($mainBook));
	}

	public function testSetGetEditionsCount()
	{
		$book = factory(Book::class)->create();
		$book->editions_count = 1;
		$book->save();

		$this->assertEquals(1, $book->editions_count);

		$book->editions_count = null;
		$book->main_book_id = null;
		$book->connected_at = null;
		$book->connect_user_id = null;
		$book->save();
		$book->refresh();

		$this->assertEquals(null, $book->editions_count);
	}

	public function testCantDeleteMainBookInGroup()
	{
		$user = factory(User::class)
			->states('admin')
			->create();

		$mainBook = factory(Book::class)->create();
		$minorBook = factory(Book::class)->create();

		BookGroupJob::dispatch($mainBook, $minorBook);

		$response = $this->actingAs($user)
			->get(route('books.delete', $mainBook))
			->assertRedirect();
		var_dump(session('errors'));
		$this->assertSessionHasErrors(__('book.you_cannot_delete_a_book_while_it_is_the_main_edition'));
	}

	public function testDeleteMinorBookAndRestoreInGroup()
	{
		$user = factory(User::class)
			->states('admin')
			->create();

		$mainBook = factory(Book::class)
			->create();

		$minorBook = factory(Book::class)
			->states('with_create_user')
			->create();

		BookGroupJob::dispatch($mainBook, $minorBook);

		$response = $this->actingAs($user)
			->get(route('books.delete', $minorBook))
			->assertRedirect();
		//var_dump(session('errors'));
		$response->assertSessionHas(['success' => __('book.deleted')]);

		$mainBook->refresh();
		$minorBook->refresh();

		$this->assertTrue($minorBook->trashed());
		$this->assertEquals(null, $mainBook->editions_count);
		$this->assertEquals(null, $minorBook->editions_count);

		$response = $this->actingAs($user)
			->get(route('books.restore', $minorBook))
			->assertRedirect();

		$mainBook->refresh();
		$minorBook->refresh();

		$this->assertFalse($minorBook->trashed());
		$this->assertEquals(1, $mainBook->editions_count);
		$this->assertEquals(1, $minorBook->editions_count);
	}

	public function testBookShowIsOkIfMainBookIsDeleted()
	{
		$mainBook = factory(Book::class)->create();
		$minorBook = factory(Book::class)->create();

		BookGroupJob::dispatch($mainBook, $minorBook);

		$minorBook->delete();
		$mainBook->delete();

		$this->get(route('books.show', $minorBook))
			->assertNotFound();
	}

	public function testUngroupMinorBookOnRestoreIfMainBookIsDeleted()
	{
		$user = factory(User::class)
			->states('admin')->create();

		$mainBook = factory(Book::class)->create();
		$minorBook = factory(Book::class)->create();

		BookGroupJob::dispatch($mainBook, $minorBook);

		$minorBook->delete();
		$mainBook->delete();

		$this->actingAs($user)
			->get(route('books.restore', $minorBook))
			->assertRedirect();

		$minorBook->refresh();

		$this->assertFalse($minorBook->isInGroup());
		$this->assertEquals(null, $minorBook->main_book_id);
		$this->assertEquals(null, $minorBook->editions_count);
	}

	public function testDeleteOkIfMainBookIsDeleted()
	{
		$user = factory(User::class)
			->states('admin')->create();

		$mainBook = factory(Book::class)
			->create();

		$minorBook = factory(Book::class)
			->states('with_create_user')
			->create();

		BookGroupJob::dispatch($mainBook, $minorBook);

		$mainBook->delete();

		$this->actingAs($user)
			->get(route('books.delete', $minorBook))
			->assertRedirect();

		$mainBook->refresh();
		$minorBook->refresh();

		$this->assertTrue($minorBook->trashed());
	}

	public function testOriginAdded()
	{
		$status = factory(BookStatus::class)
			->create(['status' => 'readed']);

		$this->assertEquals($status->book_id, $status->origin_book_id);

		$vote = factory(BookVote::class)
			->create();

		$this->assertEquals($vote->book_id, $vote->origin_book_id);

		$keyword = factory(Keyword::class)
			->create();

		$this->assertEquals($keyword->book_id, $keyword->origin_book_id);

		$comment = factory(Comment::class)
			->create();

		$this->assertEquals($comment->commentable_id, $comment->origin_commentable_id);
	}

	public function testBookListRedirectToMainEdition()
	{
		$user = factory(User::class)
			->states('admin')
			->create();

		$mainBook = factory(Book::class)->create();
		$minorBook = factory(Book::class)->create();

		BookGroupJob::dispatch($mainBook, $minorBook);

		$this->actingAs($user)
			->get(route('books.editions.index', ['book' => $minorBook->id]))
			->assertRedirect(route('books.editions.index', ['book' => $mainBook->id]));
	}

	public function testSeeMinorBookInEditionsIndex()
	{
		$user = factory(User::class)
			->states('admin')
			->create();

		$mainBook = factory(Book::class)->create();
		$minorBook = factory(Book::class)->create();

		BookGroupJob::dispatch($mainBook, $minorBook);

		$this->actingAs($user)
			->get(route('books.editions.index', ['book' => $mainBook->id]))
			->assertOk()
			->assertSeeText($minorBook->title);
	}
}
