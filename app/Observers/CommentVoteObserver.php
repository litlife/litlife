<?php

namespace App\Observers;

use App\CommentVote;

class CommentVoteObserver
{
	/**
	 * Listen to the User created event.
	 *
	 * @param CommentVote $commentVote
	 * @return void
	 */

	public function creating(CommentVote $commentVote)
	{
		$commentVote->autoAssociateAuthUser();

		if (!empty(request()->ip()))
			$commentVote->ip = request()->ip();
	}

	public function created(CommentVote $commentVote)
	{
		$this->update_votes($commentVote);
	}

	public function update_votes(&$commentVote)
	{
		$commentVoteOriginal = (object)$commentVote->getOriginal();

		if (!isset($commentVoteOriginal->vote) or $commentVoteOriginal->vote != $commentVote->vote) {

			if (!empty($commentVote->comment)) {

				$commentVote->comment->updateVotes();
			}
		}
	}

	public function deleted(CommentVote $commentVote)
	{
		$commentVote->comment->updateVotes();
	}

	public function updated(CommentVote $commentVote)
	{
		$this->update_votes($commentVote);
	}
}