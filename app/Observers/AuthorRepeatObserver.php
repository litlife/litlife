<?php

namespace App\Observers;

use App\AuthorRepeat;

class AuthorRepeatObserver
{
	/**
	 * Listen to the User created event.
	 *
	 * @param AuthorRepeat $authorRepeat
	 * @return void
	 */
	public function creating(AuthorRepeat $authorRepeat)
	{
		$authorRepeat->autoAssociateAuthUser();
	}

	public function updating(AuthorRepeat $authorRepeat)
	{

	}

	public function created(AuthorRepeat $authorRepeat)
	{
		AuthorRepeat::flushCachedOnModerationCount();
	}

	public function deleted(AuthorRepeat $authorRepeat)
	{
		AuthorRepeat::flushCachedOnModerationCount();
	}

	public function restored(AuthorRepeat $authorRepeat)
	{
		AuthorRepeat::flushCachedOnModerationCount();
	}
}