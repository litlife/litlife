<?php

namespace App\Observers;

use App\Enums\UserRelationType;
use App\Jobs\User\UpdateUserBlacklistsCount;
use App\Jobs\User\UpdateUserFriendsCount;
use App\Jobs\User\UpdateUserSubscribersCount;
use App\Jobs\User\UpdateUserSubscriptionsCount;
use App\UserRelation;

class UserRelationObserver
{
	/**
	 * Listen to the User created event.
	 *
	 * @param UserRelation $user_relation
	 * @return void
	 */
	public function saving(UserRelation $user_relation)
	{
		$backward_relation = UserRelation::where("user_id", $user_relation->user_id2)
			->where("user_id2", $user_relation->user_id)
			->first();
		/*
				if (!empty($backward_relation))
					$backward_relation->unsetEventDispatcher();
		*/
		if ($user_relation->status == UserRelationType::Subscriber) {

			// если я уже у него в подписчиках значит нужно сделать его другом
			if (
				(isset($backward_relation)) and
				(in_array($backward_relation->status, [UserRelationType::Subscriber, UserRelationType::Friend]))
			) {
				// теперь событие не будет вызываться
				UserRelation::where('id', $backward_relation->id)
					->update([
						'status' => UserRelationType::Friend,
						'user_updated_at' => now()
					]);

				$user_relation->status = UserRelationType::Friend;
			}
		}

		// если отписывается, то нужно убрать статусы друзей

		if (empty($user_relation->status)) {

			if (
				(isset($backward_relation)) and
				($backward_relation->status == UserRelationType::Friend)
			) {
				// теперь событие не будет вызываться
				UserRelation::where('id', $backward_relation->id)
					->update([
						'status' => UserRelationType::Subscriber,
						'user_updated_at' => now()
					]);
			}
		}
	}

	public function saved(UserRelation $user_relation)
	{
		// обновляем все счетчики

		if (!empty($user_relation->first_user)) {

			$user = $user_relation->first_user;

			UpdateUserFriendsCount::dispatch($user);
			UpdateUserSubscribersCount::dispatch($user);
			UpdateUserSubscriptionsCount::dispatch($user);
			UpdateUserBlacklistsCount::dispatch($user);
		}

		if (!empty($user_relation->second_user)) {

			$user = $user_relation->second_user;

			UpdateUserFriendsCount::dispatch($user);
			UpdateUserSubscribersCount::dispatch($user);
			UpdateUserSubscriptionsCount::dispatch($user);
			UpdateUserBlacklistsCount::dispatch($user);
		}

		if (isset($user_relation->first_user)) {

			unset($user_relation->first_user->relationship);
			unset($user_relation->first_user->relation);
		}

		if (isset($user_relation->second_user)) {

			unset($user_relation->second_user->relationship);
			unset($user_relation->second_user->relation);
		}
	}


}