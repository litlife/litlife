<?php

namespace App\Jobs\Book;

use App\Book;
use App\BookStatus;
use App\Jobs\User\UpdateUserBookVotesCount;
use App\Jobs\User\UpdateUserReadStatusCount;
use App\User;
use Exception;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;

class BookGroupJob
{
	use Dispatchable;

	private $book;
	private $attachableBook;
	private $updateInfo;
	private $updateCounters;
	private $updateCountersImmediately;

	/**
	 * Create a new job instance.
	 *
	 * @param Book $book
	 * @param Book $attachableBook
	 * @param boolean $updateInfo
	 * @param boolean $updateCounters
	 * @return void
	 * @throws Exception
	 */
	public function __construct(Book $book, Book $attachableBook, $updateInfo = true, $updateCounters = true, $updateCountersImmediately = true)
	{
		$this->book = $book;
		$this->attachableBook = $attachableBook;
		$this->updateInfo = $updateInfo;
		$this->updateCounters = $updateCounters;
		$this->updateCountersImmediately = $updateCountersImmediately;

		if ($this->book->is($this->attachableBook))
			throw new Exception('IDs must not match');
	}

	/**
	 * Execute the job.
	 *
	 * @return void
	 */
	public function handle()
	{
		DB::transaction(function () {

			if ($this->attachableBook->isInGroup()
				and !$this->attachableBook->isAttachedToBook($this->book)
				and $this->attachableBook->isNotMainInGroup()
			)
				BookUngroupJob::dispatch($this->attachableBook);

			$this->attachableBook->main_book_id = $this->book->id;

			if ($this->updateInfo) {
				$this->attachableBook->connected_at = now();
				$this->attachableBook->connect_user_id = auth()->id();
			}

			$this->attachableBook->push();

			if ($this->updateInfo) {
				activity()
					->performedOn($this->attachableBook)
					->withProperty('book_id', $this->book->id)
					->log('group');
			}

			$this->book->updateEditionsCount();

			$this->statuses();
			$this->votes();
			$this->keywords();
			$this->comments();

			if ($this->updateCounters) {
				$this->updateBookCounters($this->book);

				$this->book->groupedBooks()
					->update([
						'user_read_count' => $this->book->user_read_count,
						'user_read_later_count' => $this->book->user_read_later_count,
						'user_read_now_count' => $this->book->user_read_now_count,
						'user_read_not_complete_count' => $this->book->user_read_not_complete_count,
						'user_read_not_read_count' => $this->book->user_read_not_read_count,
					]);

				$this->book->refreshCommentsCount();
			}
		});
	}

	public function statuses()
	{
		BookStatus::where('book_id', $this->book->id)
			->onlyTrashed()
			->whereIn('user_id', function ($query) {
				$query->select("user_id")
					->from('book_statuses')
					->where('book_id', $this->attachableBook->id);
			})
			->forceDelete();

		$this->attachableBook
			->users_read_statuses()
			->whereNotIn('user_id', function ($query) {
				$query->select("user_id")
					->from('book_statuses')
					->where('book_id', $this->book->id);
			})
			->update([
				'origin_book_id' => DB::raw('"book_id"'),
				'book_id' => $this->book->id
			]);

		$this->attachableBook
			->users_read_statuses()
			->forceDelete();

		$this->book
			->users_read_statuses()
			->whereNull('origin_book_id')
			->update([
				'origin_book_id' => DB::raw('"book_id"')
			]);
	}

	public function votes()
	{
		$this->book->votes()
			->onlyTrashed()
			->whereIn('create_user_id', function ($query) {
				$query->select("create_user_id")
					->from('book_votes')
					->where('book_id', $this->attachableBook->id);
			})
			->forceDelete();

		$this->attachableBook
			->votes()
			->whereNotIn('create_user_id', function ($query) {
				$query->select("create_user_id")
					->from('book_votes')
					->where('book_id', $this->book->id);
			})
			->update([
				'origin_book_id' => DB::raw('"book_id"'),
				'book_id' => $this->book->id
			]);

		$this->attachableBook
			->votes()
			->delete();

		$this->book
			->votes()
			->whereNull('origin_book_id')
			->update([
				'origin_book_id' => DB::raw('"book_id"')
			]);
	}

	public function keywords()
	{
		$this->book
			->book_keywords()
			->onlyTrashed()
			->whereIn('keyword_id', function ($query) {
				$query->select("keyword_id")
					->from('book_keywords')
					->where('book_id', $this->attachableBook->id);
			})
			->forceDelete();

		$this->attachableBook
			->book_keywords()
			->whereNotIn('keyword_id', function ($query) {
				$query->select("keyword_id")
					->from('book_keywords')
					->where('book_id', $this->book->id);
			})
			->update([
				'origin_book_id' => DB::raw('"book_id"'),
				'book_id' => $this->book->id
			]);

		$this->book
			->book_keywords()
			->whereNull('origin_book_id')
			->update([
				'origin_book_id' => DB::raw('"book_id"')
			]);
	}

	public function comments()
	{
		$this->attachableBook
			->comments()
			->update([
				'origin_commentable_id' => DB::raw('"commentable_id"'),
				'commentable_id' => $this->book->id
			]);

		$this->book
			->comments()
			->whereNull('origin_commentable_id')
			->update([
				'origin_commentable_id' => DB::raw('"commentable_id"')
			]);
	}

	public function updateBookCounters(Book $book)
	{
		UpdateBookReadStatusCount::dispatch($book);
		UpdateBookRating::dispatch($book);

		if ($this->updateCountersImmediately) {

			$book->votesUsers()
				->chunkById(20, function ($users) {
					foreach ($users as $user)
						UpdateUserBookVotesCount::dispatch($user);
				});

			$book->userStatuses()
				->chunkById(20, function ($users) {
					foreach ($users as $user)
						UpdateUserReadStatusCount::dispatch($user);
				});
		} else {

			User::whereIn('id', function ($query) use ($book) {
				$query->select('create_user_id')
					->from('book_votes')
					->where('book_id', $book->id);
			})->update(['refresh_counters' => true]);

			User::whereIn('id', function ($query) use ($book) {
				$query->select('user_id')
					->from('book_statuses')
					->where('book_id', $book->id);
			})->update(['refresh_counters' => true]);

			/*
			 $book->votesUsers()
				->update(['refresh_counters' => true]);

			 $book->userStatuses()
				->update(['refresh_counters' => true]);
			 */
		}
	}
}
