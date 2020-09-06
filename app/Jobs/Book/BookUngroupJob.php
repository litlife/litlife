<?php

namespace App\Jobs\Book;

use App\Book;
use App\BookKeyword;
use App\BookStatus;
use App\BookVote;
use App\Comment;
use App\Jobs\User\UpdateUserBookVotesCount;
use App\Jobs\User\UpdateUserReadStatusCount;
use App\User;
use Exception;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;

class BookUngroupJob
{
	use Dispatchable;

	private $book;
	private $mainBook;
	private $updateCountersImmediately;

	/**
	 * Create a new job instance.
	 *
	 * @param Book $book
	 * @return void
	 */
	public function __construct(Book $book, $updateCountersImmediately = true)
	{
		$this->book = $book;

		if (!$this->book->isInGroup())
			throw new Exception('Книга не прикреплена');

		if ($this->book->isMainInGroup())
			throw new Exception('Нельзя открепить главное издание');

		$this->mainBook = $this->book->mainBook()->any()->first();
		$this->updateCountersImmediately = $updateCountersImmediately;
	}

	/**
	 * Execute the job.
	 *
	 * @return void
	 */
	public function handle()
	{
		DB::transaction(function () {

			$this->book->main_book_id = null;
			$this->book->connected_at = null;
			$this->book->connect_user_id = null;
			$this->book->editions_count = null;
			$this->book->save();

			if (!empty($this->mainBook))
				$this->mainBook->updateEditionsCount();
			/*
			$this->mainBook->main_book_id = null;
			$this->mainBook->editions_count = null;
			$this->mainBook->connected_at = null;
			$this->mainBook->connect_user_id = null;
			$this->mainBook->push();
   */
			$this->votes();
			$this->statuses();
			$this->keywords();
			$this->comments();

			if (!empty($this->mainBook))
				$this->updateBookCounters($this->mainBook);

			$this->updateBookCounters($this->book);
		});
	}

	public function votes()
	{
		BookVote::where('origin_book_id', $this->book->id)
			->whereNotIn('create_user_id', function ($query) {
				$query->select("create_user_id")
					->from('book_votes')
					->where('book_id', $this->book->id);
			})
			->update([
				'book_id' => DB::raw('"origin_book_id"')
			]);
	}

	public function statuses()
	{
		BookStatus::where('origin_book_id', $this->book->id)
			->whereNotIn('user_id', function ($query) {
				$query->select("user_id")
					->from('book_statuses')
					->where('book_id', $this->book->id);
			})
			->update([
				'book_id' => DB::raw('"origin_book_id"')
			]);
	}

	public function keywords()
	{
		BookKeyword::where('origin_book_id', $this->book->id)
			->whereNotIn('keyword_id', function ($query) {
				$query->select("keyword_id")
					->from('book_keywords')
					->where('book_id', $this->book->id);
			})
			->update([
				'book_id' => DB::raw('"origin_book_id"')
			]);
	}

	public function comments()
	{
		Comment::where('origin_commentable_id', $this->book->id)
			->bookType()
			->update([
				'commentable_id' => DB::raw('"origin_commentable_id"')
			]);
	}

	public function updateBookCounters($book)
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

		$book->refreshCommentsCount();
	}
}
