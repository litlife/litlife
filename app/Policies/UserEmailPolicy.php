<?php

namespace App\Policies;

use App\User;
use App\UserEmail;

class UserEmailPolicy extends Policy
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

	/**
	 * Можно ли пользователю включать уведомления на почтовый ящик
	 *
	 * @param User $auth_user
	 * @param UserEmail $email
	 * @return boolean
	 */

	public function notice_enable(User $auth_user, UserEmail $email)
	{
		if ($auth_user->id != $email->user->id)
			return false;

		if (!$email->confirm)
			return false;

		return true;
	}
}
