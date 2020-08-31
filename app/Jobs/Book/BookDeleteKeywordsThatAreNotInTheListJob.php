<?php

namespace App\Jobs\Book;

use App\Book;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;

class BookDeleteKeywordsThatAreNotInTheListJob
{
	use Dispatchable;

	protected $book;
	protected $keywords = [];

	/**
	 * Create a new job instance.
	 *
	 * @param Book $book
	 * @param array $keywords
	 * @return void
	 */
	public function __construct(Book $book, array $keywords)
	{
		$this->book = $book;
		$this->keywords = $keywords;
	}

	/**
	 * Execute the job.
	 *
	 * @return void
	 */
	public function handle()
	{
		DB::transaction(function () {
			$this->handleWithTransaction();
		});
	}

	public function handleWithTransaction()
	{
		foreach ($this->book->book_keywords()->with('keyword')->get() as $book_keyword) {
			if (empty($book_keyword->keyword) or !$this->isExistsInArrayOfNewKeywords($book_keyword->keyword->text)) {
				$book_keyword->delete();
			}
		}
	}

	public function isExistsInArrayOfNewKeywords($text): bool
	{
		$text = trim(mb_strtolower($text));

		foreach ($this->keywords as $keyword) {
			if (trim(mb_strtolower($keyword)) == $text)
				return true;
		}

		return false;
	}
}
