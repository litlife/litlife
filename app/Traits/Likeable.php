<?php

namespace App\Traits;

trait Likeable
{
	public function likes()
	{
		return $this->morphMany('App\Like', 'likeable');
	}

	public function authUserLike()
	{
		return $this->morphOne('App\Like', 'likeable')
			->where('create_user_id', auth()->id());
	}
}