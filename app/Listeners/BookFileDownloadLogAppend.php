<?php

namespace App\Listeners;

use App\BookFileDownloadLog;
use App\Events\BookFileHasBeenDownloaded;

class BookFileDownloadLogAppend
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
		$log = $event->bookFile->download_logs()
			->where(function ($query) {
				$query->where('user_id', auth()->id())
					->orWhere('ip', request()->ip());
			})
			->first();

		if (empty($log)) {
			$log = new BookFileDownloadLog;
			$log->user_id = auth()->id();
			$log->ip = request()->ip();

			$event->bookFile->download_logs()->save($log);
		}
	}


}
