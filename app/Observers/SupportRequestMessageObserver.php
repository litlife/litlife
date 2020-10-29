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

	/**
	 * Listen to the User created event.
	 *
	 * @param SupportRequestMessage $message
	 * @return void
	 */
	public function created(SupportRequestMessage $message)
	{
		$message->supportRequest->upadateNumberOfMessages();
		$message->supportRequest->upadateLatestMessage();
		$message->supportRequest->save();
	}
}