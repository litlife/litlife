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
			$log = UserAuthLog::orderBy('id', 'desc')
				->limit(1)
				->first();

			if (empty($log)) {
				$this->authLog($event);
			} else if ($log->user_id != $event->user->id or $log->created_at->diffInSeconds(now()) > 10) {
				$this->authLog($event);
			}
		}

		$event->user->update_activity();

		session(['show_greeting' => true]);
	}

	public function authLog($event)
	{
		$log = new UserAuthLog;
		$log->user_id = $event->user->id;
		$log->ip = request()->ip();
		$log->is_remember_me_enabled = $event->remember;
		$log->save();
	}
}
