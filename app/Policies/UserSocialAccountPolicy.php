<?php

namespace App\Policies;

use App\User;
use App\UserSocialAccount;

class UserSocialAccountPolicy extends Policy
{


	/**
	 * Create a new policy instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		//
	}

	public function detach(User $auth_user, UserSocialAccount $account)
	{
		if ($account->user_id == $auth_user->id)
			return true;
	}
}
