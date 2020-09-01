<?php

namespace App\Listeners\User;

use App\Enums\UserGroupEnum;
use App\UserGroup;

class UserAttachDefaultGroup
{
	/**
	 * Create the event listener.
	 *
	 * @return void
	 */
	public function __construct()
	{
		//
	}

	/**
	 * Handle the event.
	 *
	 * @param object $event
	 * @return void
	 */
	public function handle($event)
	{
		$event->user->load('groups');

		if ($event->user->groups->isEmpty()) {
			$group = UserGroup::where('key', UserGroupEnum::User)->firstOrFail();

			$event->user->groups()->attach($group);

			$event->user->load('groups');
		}
	}
}
