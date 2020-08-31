<?php

namespace App\Jobs;

use App\Genre;
use Illuminate\Foundation\Bus\Dispatchable;

class UpdateGenreBooksCount
{
	use Dispatchable;

	protected $genre;

	/**
	 * Create a new job instance.
	 *
	 * @param Genre $genre
	 * @return void
	 */
	public function __construct(Genre $genre)
	{
		$this->genre = $genre;
	}

	/**
	 * Execute the job.
	 *
	 * @return void
	 */
	public function handle()
	{
		$this->genre->book_count = $this->genre->books()->accepted()->count();
		$this->genre->save();
	}
}
