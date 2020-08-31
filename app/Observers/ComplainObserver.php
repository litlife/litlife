<?php

namespace App\Observers;

use App\Complain;

class ComplainObserver
{
	/**
	 * Listen to the User created event.
	 *
	 * @param Complain $complain
	 * @return void
	 */
	public function creating(Complain $complain)
	{
		$complain->autoAssociateAuthUser();
	}

	public function updating(Complain $complain)
	{

	}
}