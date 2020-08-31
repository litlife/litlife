<?php

namespace App\Policies;

use App\AuthorRepeat;
use App\User;

class AuthorRepeatPolicy extends Policy
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

	public function create(User $auth_user)
	{
		if ($auth_user->getPermission('author_repeat_report_add'))
			return true;
	}

	public function update(User $auth_user, AuthorRepeat $repeat)
	{
		if ($auth_user->getPermission('author_repeat_report_edit')) {
			if ($repeat->isUserCreator($auth_user))
				return true;
		}

		return false;
	}

	public function delete(User $auth_user, AuthorRepeat $repeat)
	{
		if ($auth_user->getPermission('author_repeat_report_delete'))
			return true;

		return false;
	}
}
