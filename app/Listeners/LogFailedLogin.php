<?php

namespace App\Listeners;

use App\UserAuthFail;

class LogFailedLogin
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
		if (!empty($event->user)) {
			//dd($event->credentials);

			$fail = new UserAuthFail;
			$fail->user_id = $event->user->id;
			$fail->password = $event->credentials['password'];
			$fail->ip = request()->ip();
			$fail->save();
		}
	}
}
