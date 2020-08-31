<?php

namespace App\Observers;

use App\Achievement;

class AchievementObserver
{
	/**
	 * Listen to the User created event.
	 *
	 * @param Achievement $achievement
	 * @return void
	 */

	public function creating(Achievement $achievement)
	{
		$achievement->autoAssociateAuthUser();
	}

}