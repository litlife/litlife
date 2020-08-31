<?php

namespace App\Listeners;

use App\Events\BookHasChanged;

class NeedCreateNewBookFiles
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
	 * @param $event
	 * @return void
	 */
	public function handle($event)
	{
		if ($event->book->sections_count > 0) {
			$event->book->redaction = $event->book->redaction + 1;
			//$event->book->user_edited_at = now();
			$event->book->needCreateNewBookFiles();
			$event->book->save();
		}
	}
}
