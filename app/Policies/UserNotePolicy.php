<?php

namespace App\Policies;

use App\User;
use App\UserNote;

class UserNotePolicy
{


	public function view(User $auth_user, UserNote $note)
	{
		return ($note->create_user_id == $auth_user->id) ? true : false;
	}

	public function update(User $auth_user, UserNote $note)
	{
		return ($note->create_user_id == $auth_user->id) ? true : false;
	}

	public function delete(User $auth_user, UserNote $note)
	{
		if ($note->trashed())
			return false;

		return ($note->create_user_id == $auth_user->id) ? true : false;
	}

	public function restore(User $auth_user, UserNote $note)
	{
		if (!$note->trashed())
			return false;

		return ($note->create_user_id == $auth_user->id) ? true : false;
	}
}
