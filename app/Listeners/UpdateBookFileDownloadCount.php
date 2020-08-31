<?php

namespace App\Listeners;

use App\Events\BookFileHasBeenDownloaded;

class UpdateBookFileDownloadCount
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
	public function handle(BookFileHasBeenDownloaded $event)
	{
		$event->bookFile->download_count = $event->bookFile->download_logs()->count();
		$event->bookFile->save();
	}
}
