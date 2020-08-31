<?php

namespace App\Observers;

use App\Award;

class AwardObserver
{
	/**
	 * Listen to the User created event.
	 *
	 * @param Award $award
	 * @return void
	 */

	public function creating(Award $award)
	{
		$award->autoAssociateAuthUser();
	}

}