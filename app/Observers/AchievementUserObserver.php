<?php

namespace App\Observers;

use App\AchievementUser;
use App\Jobs\User\UpdateUserAchievemetsCount;

class AchievementUserObserver
{
	/**
	 * Listen to the User created event.
	 *
	 * @param AchievementUser $achievementUser
	 * @return void
	 */

	public function creating(AchievementUser $achievementUser)
	{
		$achievementUser->autoAssociateAuthUser();
	}

	public function created(AchievementUser $achievementUser)
	{
		$this->refreshUserAchievementsCount($achievementUser);
	}

	private function refreshUserAchievementsCount(&$achievementUser)
	{
		UpdateUserAchievemetsCount::dispatch($achievementUser->user);
	}

	public function deleted(AchievementUser $achievementUser)
	{
		$this->refreshUserAchievementsCount($achievementUser);
	}

	public function restored(AchievementUser $achievementUser)
	{
		$this->refreshUserAchievementsCount($achievementUser);
	}
}