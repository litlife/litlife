<?php

namespace App\Traits;

trait UserAgentTrait
{
	public function user_agent()
	{
		return $this->belongsTo('App\UserAgent');
	}
}