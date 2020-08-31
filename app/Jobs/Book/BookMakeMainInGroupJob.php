<?php

namespace App\Jobs\Book;

use App\Book;
use Exception;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;

class BookMakeMainInGroupJob
{
	use Dispatchable;

	private $book;
	private $mainBook;

	/**
	 * Create a new job instance.
	 *
	 * @param Book $book
	 * @return void
	 */
	public function __construct(Book $book)
	{
		$this->book = $book;

		$this->mainBook = $this->book->mainBook;

		if (empty($this->book->main_book_id))
			throw new Exception('The book is not attached');
	}

	/**
	 * Execute the job.
	 *
	 * @return void
	 */
	public function handle()
	{
		DB::transaction(function () {

			$this->mainBook->main_book_id = $this->book->id;
			$this->mainBook->save();

			$this->mainBook->groupedBooks()->where('id', '!=', $this->book->id)
				->update([
					'main_book_id' => $this->book->id
				]);

			$this->votes();
			$this->statuses();
			$this->keywords();
			$this->comments();

			$this->book->main_book_id = null;
			$this->book->push();
		});
	}

	public function votes()
	{
		$this->mainBook->votes()
			->whereNotIn('create_user_id', function ($query) {
				$query->select("create_user_id")
					->from('book_votes')
					->where('book_id', $this->book->id);
			})
			->update([
				'book_id' => $this->book->id
			]);
	}

	public function statuses()
	{
		$this->mainBook->users_read_statuses()
			->whereNotIn('user_id', function ($query) {
				$query->select("user_id")
					->from('book_statuses')
					->where('book_id', $this->book->id);
			})
			->update([
				'book_id' => $this->book->id
			]);
	}

	public function keywords()
	{
		$this->mainBook->book_keywords()
			->whereNotIn('keyword_id', function ($query) {
				$query->select("keyword_id")
					->from('book_keywords')
					->where('book_id', $this->book->id);
			})
			->update([
				'book_id' => $this->book->id
			]);
	}

	public function comments()
	{
		$this->mainBook->comments()
			->update([
				'commentable_id' => $this->book->id
			]);
	}
}
