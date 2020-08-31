<?php

namespace App\Policies;

use App\User;

class AdminNotePolicy extends Policy
{


	public function view(User $auth_user)
	{
		if ($auth_user->getPermission('admin_comment'))
			return true;
	}

	public function create(User $auth_user)
	{
		if ($auth_user->getPermission('admin_comment'))
			return true;
	}

	public function update(User $auth_user, $admin_note)
	{
		if ($admin_note->isUserCreator($auth_user)) {
			if ($auth_user->getPermission('admin_comment'))
				return true;
		}
		return false;
	}

	public function delete(User $auth_user, $admin_note)
	{
		if ($admin_note->isUserCreator($auth_user)) {
			if ($auth_user->getPermission('admin_comment'))
				return true;
		}
		return false;
	}
}
