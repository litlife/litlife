<?php

namespace App\Jobs\Author;

use App\Author;
use Illuminate\Foundation\Bus\Dispatchable;

class UpdateAuthorBooksCount
{
	use Dispatchable;

	protected $author;

	/**
	 * Create a new job instance.
	 *
	 * @param Author $author
	 * @return void
	 */
	public function __construct(Author $author)
	{
		$this->author = $author;
	}

	/**
	 * Execute the job.
	 *
	 * @return void
	 */
	public function handle()
	{
		if ($this->author->isPrivate()) {
			$this->author->books_count = $this->author->any_books()
				->acceptedOrBelongsToUser($this->author->create_user)
				->count();
		} else {
			$this->author->books_count = $this->author->any_books()
				->accepted()
				->count();
		}

		$this->author->save();
	}
}
