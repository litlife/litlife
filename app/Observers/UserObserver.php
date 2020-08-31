<?php

namespace App\Observers;

use App\BookmarkFolder;
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

		//$this->name_helper($user);
	}

	public function updating(User $user)
	{
		//$this->name_helper($user);
	}

	public function created(User $user)
	{
		/*
				if (empty($user->data)) {
					dd($user->data);
					$data = new UserData;
					$user->data()->save($data);
				}
				*/

		if (empty($user->setting)) {
			$setting = new UserSetting;
			$user->setting()->save($setting);
		}

		$bookmarkFolder = new BookmarkFolder;
		$bookmarkFolder->title = __('bookmark_folder.default_title');
		$user->bookmark_folders()->save($bookmarkFolder);

		$user->cachedCountRefresh();

		$user->load('groups');

		if ($user->groups->isEmpty()) {
			$group = UserGroup::where('key', UserGroupEnum::User)->firstOrFail();

			$user->groups()->attach($group);

			$user->load('groups');
		}
	}

	/*
		public function deleting(Attachment $attachment)
		{
			Storage::delete($attachment->pathToFile);
		}
		*/

	public function deleted(User $user)
	{
		$user->cachedCountRefresh();
	}

	public function restored(User $user)
	{
		$user->cachedCountRefresh();
	}
	/*
		public function name_helper(User $user)
		{
			$user->name_helper = mb_substr(trim(rtrim(rtrim($user->last_name . ' ' . $user->first_name) . ' ' . $user->middle_name) . ' ' . $user->nick), 0, 255);
		}
	*/
}