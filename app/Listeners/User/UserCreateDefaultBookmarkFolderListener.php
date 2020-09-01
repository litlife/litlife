<?php

namespace App\Listeners\User;

use App\BookmarkFolder;

class UserCreateDefaultBookmarkFolderListener
{
	/**
	 * Create the event listener.
	 *
	 * @return void
	 */
	public function __construct()
	{
		//
	}

	/**
	 * Handle the event.
	 *
	 * @param object $event
	 * @return void
	 */
	public function handle($event)
	{
		if ($event->user->bookmark_folders()->count() < 1) {
			$bookmarkFolder = new BookmarkFolder;
			$bookmarkFolder->title = __('bookmark_folder.default_title');
			$event->user->bookmark_folders()->save($bookmarkFolder);
		}
	}
}
