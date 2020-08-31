<?php

namespace App\Listeners;

use Carbon\Carbon;

class PasswordResetListener
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
		$event->user->data->password_reset_count++;
		$event->user->data->last_time_password_is_reset = Carbon::now();
		$event->user->data->save();
	}
}
