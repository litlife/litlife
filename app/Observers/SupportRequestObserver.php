<?php

namespace App\Observers;

use App\SupportRequest;

class SupportRequestObserver
{
	/**
	 * Listen to the User created event.
	 *
	 * @param SupportRequest $supportRequest
	 * @return void
	 */
	public function creating(SupportRequest $supportRequest)
	{
		$supportRequest->autoAssociateAuthUser();
	}
}