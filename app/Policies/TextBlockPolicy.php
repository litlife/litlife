<?php

namespace App\Policies;

use App\Enums\TextBlockShowEnum;
use App\TextBlock;
use App\User;

class TextBlockPolicy extends Policy
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
		return (boolean)$auth_user->getPermission('text_block');
	}

	public function update(User $auth_user, TextBlock $textBlock)
	{
		return (boolean)$auth_user->getPermission('text_block');
	}

	public function delete(User $auth_user, TextBlock $textBlock)
	{
		return (boolean)$auth_user->getPermission('text_block');
	}

	public function view(?User $auth_user, TextBlock $textBlock)
	{
		if ($textBlock->show_for_all == TextBlockShowEnum::All) {
			return true;
		} elseif ($textBlock->show_for_all == TextBlockShowEnum::Administration) {
			if (empty($auth_user))
				return false;

			if ($auth_user->can('update', $textBlock))
				return true;
		}
	}
}
