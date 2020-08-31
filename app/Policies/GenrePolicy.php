<?php

namespace App\Policies;

use App\Genre;
use App\User;

class GenrePolicy extends Policy
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
		return (boolean)$auth_user->getPermission('GenreAdd');
	}

	public function update(User $auth_user, Genre $genre)
	{
		return (boolean)$auth_user->getPermission('GenreAdd');
	}

	public function delete(User $auth_user, Genre $genre)
	{
		return (boolean)$auth_user->getPermission('GenreAdd');
	}

}
