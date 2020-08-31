<?php

namespace App\Observers;

use App\Jobs\Notification\NewLikeNotificationJob;
use App\Like;

class LikeObserver
{
	/**
	 * Listen to the User created event.
	 *
	 * @param Like $like
	 * @return void
	 */
	public function saving(Like $like)
	{
		if (!empty(request()->ip()))
			$like->ip = request()->ip();

		$like->autoAssociateAuthUser();
	}

	public function created(Like $like)
	{
		$this->updateLikeableLikesCount($like->likeable);

		NewLikeNotificationJob::dispatch($like);
	}

	public function updateLikeableLikesCount($likeable)
	{
		$likeable->like_count = $likeable->likes()->count();
		//$likeable->ignoreObservableEvents();
		$likeable->save();
		//$likeable->unignoreObservableEvents();
	}

	public function deleted(Like $like)
	{
		$this->updateLikeableLikesCount($like->likeable);
	}

	public function restored(Like $like)
	{
		$this->updateLikeableLikesCount($like->likeable);
	}
}