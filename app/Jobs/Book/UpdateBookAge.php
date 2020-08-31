<?php

namespace App\Jobs\Book;

use App\Book;
use Illuminate\Foundation\Bus\Dispatchable;

class UpdateBookAge
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
		$genre_max_age = $this->book->genres()->max('age');

		if (($genre_max_age) and ($genre_max_age)) {
			if ($this->book->age < $genre_max_age) {
				$this->book->age = $genre_max_age;
			}
		}

		$this->book->save();
	}
}
