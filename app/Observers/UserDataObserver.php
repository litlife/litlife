<?php

namespace App\Observers;

use App\UserData;

class UserDataObserver
{
	/**
	 * Listen to the User created event.
	 *
	 * @param UserData $data
	 * @return void
	 */

	public function creating(UserData $data)
	{

	}

	public function updating(UserData $data)
	{

	}
}