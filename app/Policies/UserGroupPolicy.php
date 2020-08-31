<?php

namespace App\Policies;

use App\User;
use App\UserGroup;

class UserGroupPolicy extends Policy
{


	/**
	 * Create a new policy instance.
	 *
	 * @return void
	 */
	public function __construct()
	{

	}

	public function create(User $auth_user)
	{
		return (boolean)@$auth_user->getPermission('manage_users_groups');
	}

	public function update(User $auth_user, UserGroup $group)
	{
		return (boolean)@$auth_user->getPermission('manage_users_groups');
	}

	public function delete(User $auth_user, UserGroup $group)
	{
		if ($group->trashed())
			return false;

		if (!empty($group->key))
			return false;

		return (boolean)@$auth_user->getPermission('manage_users_groups');
	}

	public function restore(User $auth_user, UserGroup $group)
	{
		if (!$group->trashed())
			return false;

		return (boolean)@$auth_user->getPermission('manage_users_groups');
	}

	/**
	 * Можно ли пользователю просмотреть группы пользователей
	 *
	 * @param User $auth_user
	 * @return boolean
	 */
	public function view(User $auth_user)
	{
		return (boolean)@$auth_user->getPermission('manage_users_groups');
	}

}
