<?php

namespace App\Observers;

use App\Enums\UserGroupEnum;
use App\User;
use App\UserGroup;
use App\UserSetting;

class UserObserver
{
	/**
	 * Listen to the User created event.
	 *
	 * @param User $user
	 * @return void
	 */
	public function creating(User $user)
	{
		$user->reg_ip = request()->ip();
		$user->last_activity_at = now();
		$user->user_group_id = UserGroup::where('key', UserGroupEnum::User)->firstOrFail()->id;
	}

	public function updating(User $user)
	{
		//$this->name_helper($user);
	}

	public function created(User $user)
	{
		if (empty($user->setting)) {
			$setting = new UserSetting;
			$user->setting()->save($setting);
		}

		$user->cachedCountRefresh();
	}

	public function deleted(User $user)
	{
		$user->cachedCountRefresh();
	}

	public function restored(User $user)
	{
		$user->cachedCountRefresh();
	}

	/*
		public function deleting(Attachment $attachment)
		{
			Storage::delete($attachment->pathToFile);
		}
		*/
}