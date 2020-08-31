<?php

namespace App\Observers;

use App\ForumGroup;

class ForumGroupObserver
{
	/**
	 * Listen to the User created event.
	 *
	 * @param ForumGroup $forumGroup
	 * @return void
	 */
	public function creating(ForumGroup $forumGroup)
	{
		$forumGroup->autoAssociateAuthUser();
	}

	public function updating(ForumGroup $forumGroup)
	{
		//$forum->edit_user_id = Auth::id();
	}


	/*
		public function deleting(Attachment $attachment)
		{
			Storage::delete($attachment->pathToFile);
		}
		*/

}