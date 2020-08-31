<?php

namespace App\Jobs;

use App\BookKeyword;
use Illuminate\Foundation\Bus\Dispatchable;

class UpdateBookKeywordVotes
{
	use Dispatchable;

	protected $book_keyword;

	/**
	 * Create a new job instance.
	 *
	 * @param BookKeyword $book_keyword
	 * @return void
	 */
	public function __construct(BookKeyword $book_keyword)
	{
		$this->book_keyword = $book_keyword;
	}

	/**
	 * Execute the job.
	 *
	 * @return void
	 */
	public function handle()
	{
		$this->book_keyword->rating = $this->book_keyword->votes()->sum('vote');
		$this->book_keyword->save();
	}
}
