<?php

namespace App\Observers;

use App\UserEmailToken;
use Illuminate\Support\Str;

class UserEmailTokenObserver
{
	public function creating(UserEmailToken $token)
	{
		$token->token = Str::random(32);
	}
	/*
		public function created(UserEmailToken $token)
		{

		}

		public function updating(UserEmailToken $token)
		{

		}

		public function deleting(UserEmailToken $token)
		{

		}
	*/
}