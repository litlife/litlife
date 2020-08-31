<?php

namespace App\Jobs\Book;

use App\Book;
use Illuminate\Foundation\Bus\Dispatchable;

class UpdateBookPagesCount
{
	use Dispatchable;

	protected $book;

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
		if ($this->book->isPagesNewFormat()) {
			$this->book->page_count = $this->book->sections()
				->where('sections.type', '=', 'section')
				->sum('pages_count');
		}

		$this->book->save();
	}
}
