<?php

namespace App\Observers;

use App\Notifications\NewSupportResponseNotification;
use App\SupportQuestionMessage;

class SupportQuestionMessageObserver
{
	/**
	 * Listen to the User created event.
	 *
	 * @param SupportQuestionMessage $message
	 * @return void
	 */
	public function creating(SupportQuestionMessage $message)
	{
		$message->autoAssociateAuthUser();
	}

	/**
	 * Listen to the User created event.
	 *
	 * @param SupportQuestionMessage $message
	 * @return void
	 */
	public function created(SupportQuestionMessage $message)
	{
		$message->supportQuestion->upadateNumberOfMessages();
		$message->supportQuestion->upadateLatestMessage();
		$message->supportQuestion->save();

		if ($message->create_user->isNot($message->supportQuestion->create_user)) {
			$message->supportQuestion
				->create_user
				->notify(new NewSupportResponseNotification($message));
		}
	}
}