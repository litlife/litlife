<?php

namespace App\Listeners;

use App\User;
use Illuminate\Notifications\Events\NotificationSent;

class FlushCachedUnreadNotificationsCount
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
	 * @param NotificationSent $event
	 * @return void
	 */
	public function handle(NotificationSent $event)
	{
		if ($event->channel == 'database') {
			if ($event->notifiable instanceof User) {
				$event->notifiable->flushCachedUnreadNotificationsCount();
			}
		}
	}
}
