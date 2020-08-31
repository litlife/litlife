<?php

namespace App\Policies;

use App\Achievement;
use App\User;

class AchievementPolicy extends Policy
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
		return (bool)@$auth_user->getPermission('achievement');
	}

	public function update(User $auth_user)
	{
		return (bool)@$auth_user->getPermission('achievement');
	}

	public function delete(User $auth_user, Achievement $achievement)
	{
		if ($achievement->trashed())
			return false;

		return (bool)@$auth_user->getPermission('achievement');
	}

	public function restore(User $auth_user, Achievement $achievement)
	{
		if (!$achievement->trashed())
			return false;

		return (bool)@$auth_user->getPermission('achievement');
	}

	public function attach(User $auth_user)
	{
		return (bool)@$auth_user->getPermission('achievement');
	}

	public function detach(User $auth_user)
	{
		return (bool)@$auth_user->getPermission('achievement');
	}
}
