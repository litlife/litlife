<?php

namespace App\Observers;

use App\Forum;

class ForumObserver
{
	/**
	 * Listen to the User created event.
	 *
	 * @param Forum $forum
	 * @return void
	 */
	public function creating(Forum $forum)
	{
		$forum->autoAssociateAuthUser();
	}

	public function updating(Forum $forum)
	{
		//$forum->edit_user_id = Auth::id();
	}

	public function updated(Forum $forum)
	{
		// отключил так как теперь в запросе использую private столбец прямо из таблицы forums

		/*
		if ((intval($forum->getOriginal()['private'])) != (intval($forum->private))) {
			ForumChangePrivate::dispatch($forum, $forum->private);
		}
		*/
	}


	/*
		public function deleting(Attachment $attachment)
		{
			Storage::delete($attachment->pathToFile);
		}
		*/

}