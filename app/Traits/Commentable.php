<?php

namespace App\Traits;

trait Commentable
{
	public function refreshCommentCount()
	{
		$this->comments_count = $this->comments()->acceptedAndSentForReview()->count();
	}

	public function comments()
	{
		return $this->morphMany('App\Comment', 'commentable');
	}
}