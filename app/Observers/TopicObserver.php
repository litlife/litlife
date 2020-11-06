<?php

namespace App\Observers;

use App\Topic;
use App\User;

class TopicObserver
{
	/**
	 * Listen to the User created event.
	 *
	 * @param Topic $topic
	 * @return void
	 */
	public function creating(Topic $topic)
	{
		$topic->autoAssociateAuthUser();
	}

	public function created(Topic $topic)
	{
		if (!empty($topic->create_user))
			$this->refreshUserCreatedTopicsCount($topic->create_user);

		$this->refreshForumCounters($topic->forum);
		//event(new ForumCountOfPostsHasChanged($topic->forum));
	}

	public function refreshForumCounters($forum)
	{
		if (!empty($forum)) {

			$forum->topicsCountRefresh();
			$forum->postsCountRefresh();
			$forum->lastPostRefresh();
			$forum->save();
		}
	}

	public function updating(Topic $topic)
	{
		//$topic->edit_user_id = Auth::id();
	}

	public function deleted(Topic $topic)
	{
		if (!empty($topic->create_user))
			$this->refreshUserCreatedTopicsCount($topic->create_user);

		$this->refreshForumCounters($topic->forum);

		$topic->posts()
			->delete();
	}

	public function restoring(Topic $topic)
	{
		$topic->posts()
			->where('deleted_at', '>=', $topic->deleted_at)
			->restore();
	}

	public function restored(Topic $topic)
	{
		if (!empty($topic->create_user))
			$this->refreshUserCreatedTopicsCount($topic->create_user);

		$this->refreshForumCounters($topic->forum);
	}

	public function refreshUserCreatedTopicsCount(User $user)
	{
		$user->refreshCreatedTopicsCount();
		$user->save();
	}
}