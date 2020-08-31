<?php

namespace App\Http\Controllers;

use App\Enums\UserRelationType;
use App\Notifications\NewSubscriberNotification;
use App\User;
use App\UserRelation;
use Illuminate\Http\Response;

class UserRelationController extends Controller
{
	/**
	 * Подписаться на пользователя
	 *
	 * @param User $user
	 * @return Response
	 * @throws
	 */
	public function subscribe(User $user)
	{
		$this->authorize('subscribe', $user);

		$user_relation = UserRelation::updateOrCreate(
			['user_id' => auth()->id(), 'user_id2' => $user->id],
			['status' => UserRelationType::Subscriber, 'user_updated_at' => now()]
		);

		$user->notify(new NewSubscriberNotification(auth()->user()));

		return redirect()
			->route('profile', $user);
	}

	/**
	 * Отписаться от пользователя
	 *
	 * @param User $user
	 * @return Response
	 * @throws
	 */
	public function unsubscribe(User $user)
	{
		$this->authorize('unsubscribe', $user);

		$user_relation = UserRelation::updateOrCreate(
			['user_id' => auth()->id(), 'user_id2' => $user->id],
			['status' => UserRelationType::Null, 'user_updated_at' => now()]
		);

		return redirect()
			->route('profile', $user);
	}

	/**
	 * Добавить в черный список пользователя
	 *
	 * @param User $user
	 * @return Response
	 * @throws
	 */
	public function block(User $user)
	{
		$this->authorize('block', $user);

		$user_relation = UserRelation::updateOrCreate(
			['user_id' => auth()->id(), 'user_id2' => $user->id],
			['status' => UserRelationType::Blacklist, 'user_updated_at' => now()]
		);

		return redirect()
			->route('profile', $user);
	}

	/**
	 * Удалить из черного списка
	 *
	 * @param User $user
	 * @return Response
	 * @throws
	 */
	public function unblock(User $user)
	{
		$this->authorize('unblock', $user);

		$user_relation = UserRelation::updateOrCreate(
			['user_id' => auth()->id(), 'user_id2' => $user->id],
			['status' => UserRelationType::Null, 'user_updated_at' => now()]
		);

		return redirect()
			->route('profile', $user);
	}
}
