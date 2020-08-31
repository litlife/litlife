<?php

namespace App\Jobs\Book;

use App\Book;
use App\Library\BookSqlite;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;

class BookUpdateCharactersCountJob
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
				$this->book->characters_count = $this->book->sections()
					->where('type', 'section')
					->accepted()
					->sum('character_count');
			} else {
				$db_path = $this->book->getBookPath();

				if (file_exists($db_path)) {
					$sqlite = new BookSqlite();
					$sqlite->connect($db_path);

					$this->book->characters_count = $sqlite->getCharactersCount();
				}
			}

			$this->book->save();

			foreach ($this->book->usersAddedToFavorites as $user) {
				$user->flushCachedFavoriteBooksWithUpdatesCount();
			}

		});
	}
}
