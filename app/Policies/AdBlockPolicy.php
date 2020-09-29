<?php

namespace App\Policies;

use App\AdBlock;
use App\User;

class AdBlockPolicy extends Policy
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

	public function index(User $auth_user)
	{
		return $auth_user->getPermission('manage_ad_blocks');
	}

	public function create(User $auth_user)
	{
		return $auth_user->getPermission('manage_ad_blocks');
	}

	public function update(User $auth_user, AdBlock $adBlock)
	{
		return $auth_user->getPermission('manage_ad_blocks');
	}

	public function delete(User $auth_user, AdBlock $adBlock)
	{
		return $auth_user->getPermission('manage_ad_blocks');
	}
}
