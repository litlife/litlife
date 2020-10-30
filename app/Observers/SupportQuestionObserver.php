<?php

namespace App\Observers;

use App\SupportQuestion;

class SupportQuestionObserver
{
	/**
	 * Listen to the User created event.
	 *
	 * @param SupportQuestion $supportQuestion
	 * @return void
	 */
	public function creating(SupportQuestion $supportQuestion)
	{
		$supportQuestion->autoAssociateAuthUser();
	}
}