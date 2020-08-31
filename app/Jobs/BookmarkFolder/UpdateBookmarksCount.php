<?php

namespace App\Jobs\BookmarkFolder;

use App\BookmarkFolder;
use Illuminate\Foundation\Bus\Dispatchable;

class UpdateBookmarksCount
{
	use Dispatchable;

	protected $bookmark_folder;

	/**
	 * Create a new job instance.
	 *
	 * @param BookmarkFolder $bookmark_folder
	 * @return void
	 */
	public function __construct(BookmarkFolder $bookmark_folder)
	{
		$this->bookmark_folder = $bookmark_folder;
	}

	/**
	 * Execute the job.
	 *
	 * @return void
	 */
	public function handle()
	{
		$this->bookmark_folder->bookmark_count = $this->bookmark_folder->bookmarks()->count();
		$this->bookmark_folder->save();
	}
}
