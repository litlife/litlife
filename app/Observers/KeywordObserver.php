<?php

namespace App\Observers;

use App\Keyword;

class KeywordObserver
{
	/**
	 * Listen to the User created event.
	 *
	 * @param Keyword $keyword
	 * @return void
	 */
	public function creating(Keyword $keyword)
	{
		$keyword->autoAssociateAuthUser();
	}

	public function restoring(Keyword $keyword)
	{

	}


	/*
		public function deleting(Attachment $attachment)
		{
			Storage::delete($attachment->pathToFile);
		}
		*/

}