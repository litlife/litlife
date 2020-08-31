<?php

namespace App\Jobs\Author;

use App\Author;
use Illuminate\Foundation\Bus\Dispatchable;

class UpdateAuthorCommentsCount
{
	use Dispatchable;

	protected $author;

	/**
	 * Create a new job instance.
	 *
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
		$this->author->comments_count = $this->author->any_books()->sum('comment_count');
		$this->author->save();
	}
}
