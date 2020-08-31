<?php

namespace App\Jobs\Book;

use App\Book;
use App\Library\BookSqlite;
use App\Section;
use Exception;
use Illuminate\Foundation\Bus\Dispatchable;

class UpdateBookSectionsCount
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
			$this->book->sections_count = intval(Section::where('book_id', $this->book->id)
				->accepted()
				->chapter()
				->count());
		} else {
			$db_path = $this->book->getBookPath();

			if (file_exists($db_path)) {
				try {
					$sqlite = new BookSqlite();
					$sqlite->connect($db_path);

					$sections_count = $sqlite->sectionsCount();

					$this->book->sections_count = intval($sections_count);

				} catch (Exception $exception) {

				}
			} else {
				$this->book->sections_count = 0;
			}
		}
		//$this->book->ignoreObservableEvents();
		$this->book->save();
		//$this->book->unignoreObservableEvents();
	}
}
