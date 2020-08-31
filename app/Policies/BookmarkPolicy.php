<?php

namespace App\Policies;

use App\Bookmark;
use App\User;

class BookmarkPolicy extends Policy
{


	public function update(User $auth_user, Bookmark $bookmark)
	{
		return (bool)$bookmark->isUserCreator($auth_user);
	}

	public function delete(User $auth_user, Bookmark $bookmark)
	{
		if ($bookmark->trashed())
			return false;

		return (bool)$bookmark->isUserCreator($auth_user);
	}


	public function restore(User $auth_user, Bookmark $bookmark)
	{
		if (!$bookmark->trashed())
			return false;

		return (bool)$bookmark->isUserCreator($auth_user);
	}

}
