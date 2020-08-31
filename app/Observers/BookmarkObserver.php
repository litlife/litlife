<?php

namespace App\Observers;

use App\Bookmark;
use App\BookmarkFolder;
use App\Jobs\BookmarkFolder\UpdateBookmarksCount;

class BookmarkObserver
{
	/**
	 * Listen to the User created event.
	 *
	 * @param Bookmark $bookmark
	 * @return void
	 */
	public function creating(Bookmark $bookmark)
	{
		$bookmark->autoAssociateAuthUser();
	}

	public function created(Bookmark $bookmark)
	{
		$this->updateFolderBookmarksCount($bookmark);
	}

	public function updateFolderBookmarksCount($bookmark)
	{
		if (!empty($bookmark->folder))
			UpdateBookmarksCount::dispatch($bookmark->folder);
	}

	public function updated(Bookmark $bookmark)
	{
		if (array_key_exists('folder_id', $bookmark->getChanges()))
			$this->updateFolderBookmarksCount($bookmark);

		if (!empty($bookmark->getOriginal('folder_id'))) {
			if (!empty($bookmark_folder = BookmarkFolder::find($bookmark->getOriginal('folder_id'))))
				UpdateBookmarksCount::dispatch($bookmark_folder);
		}

	}

	public function deleted(Bookmark $bookmark)
	{
		$this->updateFolderBookmarksCount($bookmark);
	}
}