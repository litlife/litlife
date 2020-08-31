<?php

namespace App\Jobs\Book;

use App\Book;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;

class BookUpdatePageNumbersJob
{
	use Dispatchable;

	private $book;

	/**
	 * Create a new job instance.
	 *
	 * @param Book $book
	 * @return void
	 */
	public function __construct(Book $book)
	{
		$this->book = $book;
	}

	/**
	 * Execute the job.
	 *
	 * @return void
	 */
	public function handle()
	{
		DB::transaction(function () {

			if ($this->book->isPagesNewFormat()) {

				$chapters = $this->book->sections()
					->chapter()
					->with('pages')
					->defaultOrder()
					->get();

				$number = 0;

				foreach ($chapters as $chapter) {
					foreach ($chapter->pages as $page) {
						$number++;

						$page->book_page = $number;
						$page->save();
					}
				}

				$this->book->page_count = $number;
				$this->book->save();
			}

		});
	}
}
