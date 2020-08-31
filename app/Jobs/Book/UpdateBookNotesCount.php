<?php

namespace App\Jobs\Book;

use App\Book;
use App\Section;
use Illuminate\Foundation\Bus\Dispatchable;

class UpdateBookNotesCount
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
		$this->book->notes_count = intval(Section::where('book_id', $this->book->id)->where('type', 'note')->count());
		$this->book->save();
	}
}
