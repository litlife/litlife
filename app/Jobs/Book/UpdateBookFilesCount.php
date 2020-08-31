<?php

namespace App\Jobs\Book;

use App\Book;
use Illuminate\Foundation\Bus\Dispatchable;

class UpdateBookFilesCount
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
		if ($this->book->isPrivate()) {
			$this->book->files_count = $this->book->files()
				->acceptedOrBelongsToUser($this->book->create_user)
				->count();

			$formats = $this->book->files()
				->acceptedOrBelongsToUser($this->book->create_user)
				->select('id', 'format')
				->pluck('format')
				->unique()
				->toArray();
		} else {
			$this->book->files_count = $this->book->files()
				->accepted()
				->count();

			$formats = $this->book->files()
				->accepted()
				->select('id', 'format')
				->pluck('format')
				->unique()
				->toArray();
		}

		if (empty($formats))
			$formats = null;

		$this->book->formats = $formats;
		$this->book->save();
	}
}
