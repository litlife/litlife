<?php

namespace Tests\Feature\Book;

use App\Author;
use App\Book;
use App\BookStatus;
use App\BookVote;
use App\Jobs\Author\UpdateAuthorRating;
use App\Jobs\Book\UpdateBookRating;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BookVoteTest extends TestCase
{
	public function testVote()
	{
		$user = factory(User::class)
			->create();
		$user->group->vote_for_book = true;
		$user->push();

		$book = factory(Book::class)->create();
		$book->statusAccepted();
		$book->save();

		$old_time = now()->subMinutes(10);

		$book_read_status = factory(BookStatus::class)->create([
			'book_id' => $book->id,
			'user_id' => $user->id,
			'status' => 'read_now',
			'user_updated_at' => $old_time
		]);

		$number = rand(1, 10);

		$response = $this->actingAs($user)
			->get(route('books.vote', [
				'book' => $book->id,
				'vote' => $number
			]))
			->assertRedirect(route('books.show', ['book' => $book]));

		$response->assertRedirect();

		$vote = $book->votes()->first();

		$this->assertNotNull($vote);
		$this->assertEquals($number, $vote->vote);
		$this->assertNotNull($vote->user_updated_at);
		$this->assertEquals($book->id, $vote->origin_book_id);
		$this->assertEquals($vote->book_id, $vote->origin_book_id);

		$book_read_status = $user->book_read_statuses()->first();

		$this->assertNotNull($book_read_status);
		$this->assertEquals('readed', $book_read_status->status);
		$this->assertNotEquals($old_time->timestamp, $book_read_status->user_updated_at->timestamp);
		$this->assertGreaterThan($old_time, $book_read_status->user_updated_at);
		$this->assertEquals($book->id, $book_read_status->origin_book_id);
	}

	public function testUpdateBookRating()
	{
		Storage::fake(config('filesystems.default'));

		$book = factory(Book::class)->create();

		$compiler = factory(Author::class)->create();
		$compiler->save();

		$illustrator = factory(Author::class)->create();
		$illustrator->save();

		$book->compilers()->syncWithoutDetaching([$compiler->id]);
		$book->illustrators()->syncWithoutDetaching([$illustrator->id]);

		UpdateBookRating::dispatch($book);

		$this->assertFalse($book->isRatingChanged());

		$book_vote = factory(BookVote::class)
			->create([
				'book_id' => $book->id, 'vote' => '8',
				'create_user_id' => factory(User::class)->create(['gender' => 'female'])->id
			]);

		$book_vote2 = factory(BookVote::class)
			->create([
				'book_id' => $book->id, 'vote' => '6',
				'create_user_id' => factory(User::class)->create(['gender' => 'male'])->id
			]);

		$book_vote3 = factory(BookVote::class)
			->create([
				'book_id' => $book->id, 'vote' => '4',
				'create_user_id' => factory(User::class)->create(['gender' => 'female'])->id
			]);

		UpdateBookRating::dispatch($book);

		$book->refresh();

		$this->assertEquals('6', $book->vote_average);
		$this->assertEquals('3', $book->user_vote_count);
		$this->assertEquals('0', $book->in_rating);
		$this->assertIsArray($book->rate_info);
		$this->assertEquals('100', $book->rate_info[8]['percent']);
		$this->assertEquals('1', $book->rate_info[8]['count']);
		$this->assertEquals('100', $book->rate_info[6]['percent']);
		$this->assertEquals('1', $book->rate_info[6]['count']);
		$this->assertEquals('1', $book->male_vote_count);
		$this->assertEquals('2', $book->female_vote_count);
		$this->assertEquals('33.3333', $book->male_vote_percent);
		$this->assertFalse($book->isRatingChanged());

		foreach ($book->authors()->get() as $author) {
			$this->assertTrue($author->rating_changed);
		}

		foreach ($book->authors()->get() as $author) {
			UpdateAuthorRating::dispatch($author);

			$this->assertEquals($book->user_vote_count, $author->votes_count);
			$this->assertEquals($book->vote_average, $author->vote_average);
			$this->assertEquals(18, $author->rating);
			$this->assertFalse($author->isRatingChanged());
		}

		foreach ($book->illustrators()->get() as $author) {
			$this->assertEquals($book->user_vote_count, $author->votes_count);
			$this->assertEquals($book->vote_average, $author->vote_average);
			$this->assertEquals(18, $author->rating);
		}

		$this->assertEquals(18, $book->average_rating_for_period->all_rating);
	}

	public function testAverageRatingForPeriod()
	{
		$vote = 7;
		$vote2 = 3;

		$now = now();

		$book_vote = factory(BookVote::class)
			->create(['vote' => $vote, 'created_at' => $now]);

		$book = $book_vote->book;

		$book_vote2 = factory(BookVote::class)
			->create(['vote' => $vote2, 'created_at' => $now, 'book_id' => $book->id]);

		UpdateBookRating::dispatch($book);

		$this->assertEquals(5, $book->average_rating_for_period->day_vote_average);
		$this->assertEquals(2, $book->average_rating_for_period->day_votes_count);
		$this->assertEquals(10, $book->average_rating_for_period->day_rating);
		$this->assertEquals(5, $book->average_rating_for_period->week_vote_average);
		$this->assertEquals(2, $book->average_rating_for_period->week_votes_count);
		$this->assertEquals(10, $book->average_rating_for_period->week_rating);
		$this->assertEquals(5, $book->average_rating_for_period->month_vote_average);
		$this->assertEquals(2, $book->average_rating_for_period->month_votes_count);
		$this->assertEquals(10, $book->average_rating_for_period->month_rating);
		$this->assertEquals(5, $book->average_rating_for_period->quarter_vote_average);
		$this->assertEquals(2, $book->average_rating_for_period->quarter_votes_count);
		$this->assertEquals(10, $book->average_rating_for_period->quarter_rating);
		$this->assertEquals(5, $book->average_rating_for_period->year_vote_average);
		$this->assertEquals(2, $book->average_rating_for_period->year_votes_count);
		$this->assertEquals(10, $book->average_rating_for_period->year_rating);

		Artisan::call('refresh:clear_rating_for_periods');
		$book->refresh();

		$this->assertEquals(5, $book->average_rating_for_period->day_vote_average);
		$this->assertEquals(2, $book->average_rating_for_period->day_votes_count);
		$this->assertEquals(10, $book->average_rating_for_period->day_rating);
		$this->assertEquals(5, $book->average_rating_for_period->week_vote_average);
		$this->assertEquals(2, $book->average_rating_for_period->week_votes_count);
		$this->assertEquals(10, $book->average_rating_for_period->week_rating);
		$this->assertEquals(5, $book->average_rating_for_period->month_vote_average);
		$this->assertEquals(2, $book->average_rating_for_period->month_votes_count);
		$this->assertEquals(10, $book->average_rating_for_period->month_rating);
		$this->assertEquals(5, $book->average_rating_for_period->quarter_vote_average);
		$this->assertEquals(2, $book->average_rating_for_period->quarter_votes_count);
		$this->assertEquals(10, $book->average_rating_for_period->quarter_rating);
		$this->assertEquals(5, $book->average_rating_for_period->year_vote_average);
		$this->assertEquals(2, $book->average_rating_for_period->year_votes_count);
		$this->assertEquals(10, $book->average_rating_for_period->year_rating);

		Carbon::setTestNow($now->addDay()->addMinute());

		Artisan::call('refresh:clear_rating_for_periods');
		$book->refresh();

		$this->assertEquals(0, $book->average_rating_for_period->day_vote_average);
		$this->assertEquals(0, $book->average_rating_for_period->day_votes_count);
		$this->assertEquals(0, $book->average_rating_for_period->day_rating);
		$this->assertEquals(5, $book->average_rating_for_period->week_vote_average);
		$this->assertEquals(2, $book->average_rating_for_period->week_votes_count);
		$this->assertEquals(10, $book->average_rating_for_period->week_rating);
		$this->assertEquals(5, $book->average_rating_for_period->month_vote_average);
		$this->assertEquals(2, $book->average_rating_for_period->month_votes_count);
		$this->assertEquals(10, $book->average_rating_for_period->month_rating);
		$this->assertEquals(5, $book->average_rating_for_period->quarter_vote_average);
		$this->assertEquals(2, $book->average_rating_for_period->quarter_votes_count);
		$this->assertEquals(10, $book->average_rating_for_period->quarter_rating);
		$this->assertEquals(5, $book->average_rating_for_period->year_vote_average);
		$this->assertEquals(2, $book->average_rating_for_period->year_votes_count);
		$this->assertEquals(10, $book->average_rating_for_period->year_rating);

		Carbon::setTestNow($now->addWeek()->addMinute());

		Artisan::call('refresh:clear_rating_for_periods');
		$book->refresh();

		$this->assertEquals(0, $book->average_rating_for_period->day_vote_average);
		$this->assertEquals(0, $book->average_rating_for_period->day_votes_count);
		$this->assertEquals(0, $book->average_rating_for_period->day_rating);
		$this->assertEquals(0, $book->average_rating_for_period->week_vote_average);
		$this->assertEquals(0, $book->average_rating_for_period->week_votes_count);
		$this->assertEquals(0, $book->average_rating_for_period->week_rating);
		$this->assertEquals(5, $book->average_rating_for_period->month_vote_average);
		$this->assertEquals(2, $book->average_rating_for_period->month_votes_count);
		$this->assertEquals(10, $book->average_rating_for_period->month_rating);
		$this->assertEquals(5, $book->average_rating_for_period->quarter_vote_average);
		$this->assertEquals(2, $book->average_rating_for_period->quarter_votes_count);
		$this->assertEquals(10, $book->average_rating_for_period->quarter_rating);
		$this->assertEquals(5, $book->average_rating_for_period->year_vote_average);
		$this->assertEquals(2, $book->average_rating_for_period->year_votes_count);
		$this->assertEquals(10, $book->average_rating_for_period->year_rating);

		Carbon::setTestNow($now->addMonth()->addMinute());

		Artisan::call('refresh:clear_rating_for_periods');
		$book->refresh();

		$this->assertEquals(0, $book->average_rating_for_period->day_vote_average);
		$this->assertEquals(0, $book->average_rating_for_period->day_votes_count);
		$this->assertEquals(0, $book->average_rating_for_period->day_rating);
		$this->assertEquals(0, $book->average_rating_for_period->week_vote_average);
		$this->assertEquals(0, $book->average_rating_for_period->week_votes_count);
		$this->assertEquals(0, $book->average_rating_for_period->week_rating);
		$this->assertEquals(0, $book->average_rating_for_period->month_vote_average);
		$this->assertEquals(0, $book->average_rating_for_period->month_votes_count);
		$this->assertEquals(0, $book->average_rating_for_period->month_rating);
		$this->assertEquals(5, $book->average_rating_for_period->quarter_vote_average);
		$this->assertEquals(2, $book->average_rating_for_period->quarter_votes_count);
		$this->assertEquals(10, $book->average_rating_for_period->quarter_rating);
		$this->assertEquals(5, $book->average_rating_for_period->year_vote_average);
		$this->assertEquals(2, $book->average_rating_for_period->year_votes_count);
		$this->assertEquals(10, $book->average_rating_for_period->year_rating);

		Carbon::setTestNow($now->addQuarter()->addMinute());

		Artisan::call('refresh:clear_rating_for_periods');
		$book->refresh();

		$this->assertEquals(0, $book->average_rating_for_period->day_vote_average);
		$this->assertEquals(0, $book->average_rating_for_period->day_votes_count);
		$this->assertEquals(0, $book->average_rating_for_period->day_rating);
		$this->assertEquals(0, $book->average_rating_for_period->week_vote_average);
		$this->assertEquals(0, $book->average_rating_for_period->week_votes_count);
		$this->assertEquals(0, $book->average_rating_for_period->week_rating);
		$this->assertEquals(0, $book->average_rating_for_period->month_vote_average);
		$this->assertEquals(0, $book->average_rating_for_period->month_votes_count);
		$this->assertEquals(0, $book->average_rating_for_period->month_rating);
		$this->assertEquals(0, $book->average_rating_for_period->quarter_vote_average);
		$this->assertEquals(0, $book->average_rating_for_period->quarter_votes_count);
		$this->assertEquals(0, $book->average_rating_for_period->quarter_rating);
		$this->assertEquals(5, $book->average_rating_for_period->year_vote_average);
		$this->assertEquals(2, $book->average_rating_for_period->year_votes_count);
		$this->assertEquals(10, $book->average_rating_for_period->year_rating);

		Carbon::setTestNow($now->addYear()->addMinute());

		Artisan::call('refresh:clear_rating_for_periods');
		$book->refresh();

		$this->assertEquals(0, $book->average_rating_for_period->day_vote_average);
		$this->assertEquals(0, $book->average_rating_for_period->day_votes_count);
		$this->assertEquals(0, $book->average_rating_for_period->day_rating);
		$this->assertEquals(0, $book->average_rating_for_period->week_vote_average);
		$this->assertEquals(0, $book->average_rating_for_period->week_votes_count);
		$this->assertEquals(0, $book->average_rating_for_period->week_rating);
		$this->assertEquals(0, $book->average_rating_for_period->month_vote_average);
		$this->assertEquals(0, $book->average_rating_for_period->month_votes_count);
		$this->assertEquals(0, $book->average_rating_for_period->month_rating);
		$this->assertEquals(0, $book->average_rating_for_period->quarter_vote_average);
		$this->assertEquals(0, $book->average_rating_for_period->quarter_votes_count);
		$this->assertEquals(0, $book->average_rating_for_period->quarter_rating);
		$this->assertEquals(0, $book->average_rating_for_period->year_vote_average);
		$this->assertEquals(0, $book->average_rating_for_period->year_votes_count);
		$this->assertEquals(0, $book->average_rating_for_period->year_rating);
	}

	public function testVoteRemove()
	{
		$vote = factory(BookVote::class)
			->create();

		$book = $vote->book;
		$book->refresh_rating = false;
		$book->save();

		$user = $vote->create_user;
		$user->gender = 'male';
		$user->group->vote_for_book = true;
		$user->push();

		UpdateBookRating::dispatch($book);

		$book->refresh();
		$user->refresh();

		$this->assertEquals(1, $user->book_rate_count);
		$this->assertEquals(1, $book->user_vote_count);
		$this->assertFalse($book->isRatingChanged());
		$this->assertEquals(1, $book->male_vote_count);
		$this->assertEquals(0, $book->female_vote_count);

		$this->actingAs($user)
			->get(route('books.votes.delete', ['book' => $book]))
			->assertRedirect(route('books.show', ['book' => $book]));

		UpdateBookRating::dispatch($book);

		$book->refresh();
		$vote->refresh();
		$user->refresh();

		$this->assertTrue($book->isRatingChanged());
		$this->assertSoftDeleted($vote);

		$this->assertEquals(0, $user->book_rate_count);
		$this->assertEquals(0, $book->user_vote_count);
		$this->assertEquals(0, $book->male_vote_count);
		$this->assertEquals(0, $book->female_vote_count);
	}

	public function testUserBooksVoteCount()
	{
		$vote = factory(BookVote::class)->create();

		$user = $vote->create_user;

		$this->assertEquals(1, $user->book_rate_count);

		$vote->delete();
		$user->refresh();

		$this->assertEquals(0, $user->book_rate_count);
	}

	public function testRestoreIfDeleted()
	{
		$vote = factory(BookVote::class)
			->create(['vote' => 3, 'ip' => '1.1.1.1']);
		$vote->delete();

		$user_updated_at = $vote->user_updated_at;

		$this->assertSoftDeleted($vote);

		$user = $vote->create_user;
		$user->group->vote_for_book = true;
		$user->push();

		$book = $vote->book;

		Carbon::setTestNow(now()->addDay());

		$response = $this->actingAs($user)
			->get(route('books.vote', [
				'book' => $book->id,
				'vote' => 5
			]), ['REMOTE_ADDR' => '2.2.2.2'])
			->assertRedirect();

		$vote->refresh();

		$this->assertFalse($vote->trashed());
		$this->assertEquals(5, $vote->vote);
		$this->assertEquals('2.2.2.2', $vote->ip);
		$this->assertNotEquals($user_updated_at, $vote->user_updated_at);
	}

	public function testMaxMinVote()
	{
		$vote = factory(BookVote::class)
			->create(['vote' => 15]);

		$this->assertEquals(10, $vote->vote);

		$vote = factory(BookVote::class)
			->create(['vote' => 0]);

		$this->assertEquals(1, $vote->vote);

		$number = rand(2, 8);

		$vote = factory(BookVote::class)
			->create(['vote' => $number]);

		$this->assertEquals($number, $vote->vote);
	}

	public function testUpdateHttp()
	{
		$number = rand(2, 6);

		$vote = factory(BookVote::class)
			->create(['vote' => 9, 'ip' => '1.1.1.1']);

		$user = $vote->create_user;
		$user->group->vote_for_book = true;
		$user->push();
		$book = $vote->book;

		$this->assertEquals(1, $user->book_rate_count);
		$this->assertEquals(1, $book->user_vote_count);
		$this->assertNotNull($vote->user_updated_at);

		$user_updated_at = $vote->user_updated_at;

		Carbon::setTestNow(now()->addDay());

		$response = $this->actingAs($user)
			->get(route('books.vote', [
				'book' => $book->id,
				'vote' => $number
			]), ['REMOTE_ADDR' => '2.2.2.2'])
			->assertRedirect();

		$vote->refresh();
		$book->refresh();

		$this->assertEquals(1, $user->book_rate_count);
		$this->assertEquals(1, $book->user_vote_count);
		$this->assertNotEquals($user_updated_at, $vote->user_updated_at);
		$this->assertEquals($number, $vote->vote);
		$this->assertEquals('2.2.2.2', $vote->ip);
		$this->assertTrue($book->isRatingChanged());
	}


	public function testCreateReadedStatusOnVote()
	{
		$user = factory(User::class)->create();
		$user->group->vote_for_book = true;
		$user->push();

		$book = factory(Book::class)->create();

		$response = $this->actingAs($user)
			->get(route('books.vote', [
				'book' => $book->id,
				'vote' => 9
			]))
			->assertRedirect(route('books.show', ['book' => $book]));

		$vote = $book->votes()->first();

		$book_read_status = $user->book_read_statuses()->first();

		$this->assertNotNull($book_read_status);
		$this->assertEquals('readed', $book_read_status->status);
	}

	public function testDontChangeReadStatusIfItExists()
	{
		$user = factory(User::class)->create();
		$user->group->vote_for_book = true;
		$user->push();

		$book = factory(Book::class)->create();

		$oldTime = now()->subDay();

		$book_read_status = factory(BookStatus::class)
			->create([
				'book_id' => $book->id,
				'user_id' => $user->id,
				'status' => 'read_not_complete',
				'user_updated_at' => $oldTime
			]);

		$response = $this->actingAs($user)
			->get(route('books.vote', [
				'book' => $book->id,
				'vote' => 9
			]))
			->assertRedirect(route('books.show', ['book' => $book]));

		$vote = $book->votes()->first();

		$this->assertNotNull($vote);
		$this->assertEquals(9, $vote->vote);

		$book_read_status->refresh();

		$this->assertNotNull($book_read_status);
		$this->assertEquals('read_not_complete', $book_read_status->status);
		$this->assertEquals($oldTime->timestamp, $book_read_status->user_updated_at->timestamp);
	}

	public function testDontChangeReadedStatusTime()
	{
		$user = factory(User::class)->create();
		$user->group->vote_for_book = true;
		$user->push();

		$book = factory(Book::class)->create();

		$oldTime = now()->subDay();

		$book_read_status = factory(BookStatus::class)
			->create([
				'book_id' => $book->id,
				'user_id' => $user->id,
				'status' => 'readed',
				'user_updated_at' => $oldTime
			]);

		$response = $this->actingAs($user)
			->get(route('books.vote', [
				'book' => $book->id,
				'vote' => 9
			]))
			->assertRedirect(route('books.show', ['book' => $book]));

		$book_read_status->refresh();

		$this->assertNotNull($book_read_status);
		$this->assertEquals('readed', $book_read_status->status);
		$this->assertEquals($oldTime->timestamp, $book_read_status->user_updated_at->timestamp);
	}

	public function testChangeReadLaterToReaded()
	{
		$user = factory(User::class)->create();
		$user->group->vote_for_book = true;
		$user->push();

		$book = factory(Book::class)->create();

		$oldTime = now()->subDay();

		$book_read_status = factory(BookStatus::class)
			->create([
				'book_id' => $book->id,
				'user_id' => $user->id,
				'status' => 'read_later',
				'user_updated_at' => $oldTime
			]);

		$response = $this->actingAs($user)
			->get(route('books.vote', [
				'book' => $book->id,
				'vote' => 9
			]))
			->assertRedirect(route('books.show', ['book' => $book]));

		$book_read_status->refresh();

		$this->assertNotNull($book_read_status);
		$this->assertEquals('readed', $book_read_status->status);
		$this->assertNotEquals($oldTime->timestamp, $book_read_status->user_updated_at->timestamp);
	}

	public function testRateInfo()
	{
		$book = factory(Book::class)->create();

		$inputArray = [
			'10' => '100',
			'9' => '101',
			'8' => '102',
			'7' => '103',
			'6' => '104',
			'5' => '105',
			'4' => '106',
			'3' => '107',
			'2' => '108',
			'1' => '109',
		];

		$book->rate_info = $inputArray;
		$array = $book->rate_info;

		$assertArray = [
			'10' => ['count' => '100', 'percent' => '92'],
			'9' => ['count' => '101', 'percent' => '93'],
			'8' => ['count' => '102', 'percent' => '94'],
			'7' => ['count' => '103', 'percent' => '94'],
			'6' => ['count' => '104', 'percent' => '95'],
			'5' => ['count' => '105', 'percent' => '96'],
			'4' => ['count' => '106', 'percent' => '97'],
			'3' => ['count' => '107', 'percent' => '98'],
			'2' => ['count' => '108', 'percent' => '99'],
			'1' => ['count' => '109', 'percent' => '100']
		];

		$this->assertEquals($assertArray, $array);

		$book->rate_info = $array;
		$array = $book->rate_info;

		$this->assertEquals($assertArray, $array);

		$book->rate_info = $array;
		$array = $book->rate_info;

		$this->assertEquals($assertArray, $array);
	}

	public function testGetEmptyRateInfo()
	{
		$book = factory(Book::class)->create();

		$this->assertEquals(0, $book->rate_info[10]['count']);
		$this->assertEquals(0, $book->rate_info[1]['count']);
	}

	public function testDontCountDeletedUserVote()
	{
		$vote = factory(BookVote::class)
			->create(['vote' => 5]);

		$book = $vote->book;
		$user = $vote->create_user;
		$user->gender = 'male';
		$user->save();

		dispatch(new UpdateBookRating($book));

		$this->assertEquals(1, $book->user_vote_count);
		$this->assertEquals(1, $book->male_vote_count);
		$this->assertEquals(0, $book->female_vote_count);
		$this->assertEquals(5, $book->vote_average);
		$this->assertEquals(5, $book->vote_average);

		$this->assertEquals(100, $book->rate_info[5]['percent']);
		$this->assertEquals(1, $book->rate_info[5]['count']);

		$user->delete();

		dispatch(new UpdateBookRating($book));

		$this->assertEquals(0, $book->user_vote_count);
		$this->assertEquals(0, $book->male_vote_count);
		$this->assertEquals(0, $book->female_vote_count);
		$this->assertEquals(0, $book->vote_average);
		$this->assertEquals(0, $book->vote_average);

		$this->assertEquals(0, $book->rate_info[5]['percent']);
		$this->assertEquals(0, $book->rate_info[5]['count']);
	}

	public function testDontSeeUserVoteIfVoteDeleted()
	{
		$vote = factory(BookVote::class)
			->create(['vote' => 5]);

		$vote_user = $vote->create_user;
		$book = $vote->book;

		$vote->delete();

		$user = factory(User::class)->create();

		$this->actingAs($user)
			->get(route('books.votes', $book))
			->assertOk()
			->assertDontSeeText($vote_user->nick);
	}

	public function testShowAskUserToRateBookIfStatusReadedAndBookVoteEmpty()
	{
		$book_read_status = factory(BookStatus::class)->create();
		$book_read_status->status = 'readed';
		$book_read_status->save();

		$user = $book_read_status->user;
		$book = $book_read_status->book;

		$this->actingAs($user)
			->get(route('books.show', $book))
			->assertOk()
			->assertViewHas(['ask_user_to_rate_the_book' => true]);
	}
}
