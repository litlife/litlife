<?php

namespace App\Listeners;

use App\UserAuthLog;

class LogSuccessfulLogin
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
		if ($event->user->confirmed_mailbox_count < 1) {
			$event->user->refreshConfirmedMailboxCount();
			$event->user->save();
		}

		if (!empty($event->user)) {
			$success = new UserAuthLog;
			$success->user_id = $event->user->id;
			$success->ip = request()->ip();
			$success->save();
		}

		$event->user->update_activity();

		session(['show_greeting' => true]);
	}
}
