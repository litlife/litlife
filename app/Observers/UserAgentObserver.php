<?php

namespace App\Observers;

use App\UserAgent;
use Browser;

class UserAgentObserver
{
	/**
	 * Listen to the User created event.
	 *
	 * @param UserAgent $agent
	 * @return void
	 */
	public function creating(UserAgent $agent)
	{
		//dd('123');

		if (empty($agent->value))
			$agent->value = Browser::userAgent();
	}
}