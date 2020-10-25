<?php

namespace App\Observers;

use App\SupportRequestMessage;

class SupportRequestMessageObserver
{
	/**
	 * Listen to the User created event.
	 *
	 * @param SupportRequestMessage $message
	 * @return void
	 */
	public function creating(SupportRequestMessage $message)
	{
		$message->autoAssociateAuthUser();
	}
}