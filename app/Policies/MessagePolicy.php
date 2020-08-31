<?php

namespace App\Policies;

use App\Message;
use App\User;

class MessagePolicy extends Policy
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

	/**
	 * Редактировать сообщение
	 *
	 * @return boolean
	 */

	public function update(User $auth_user, Message $message)
	{
		// сообщение уже прочитано
		//if ($message->isViewed())
		//    return false;

		// сообщение не принадлежит пользователю, поэтому запрещено
		if (!$message->isUserCreator($auth_user))
			return false;

		// не прошлои ли время в течениe которого можно редактироват сообщение
		if (now() > $message->created_at->addMinutes(config('litlife.time_that_can_edit_message')))
			return false;

		return true;

	}

	/**
	 * Удалить сообщение
	 *
	 * @param User $auth_user , Message $message
	 * @return boolean
	 */

	public function delete(User $auth_user, Message $message)
	{
		if ($message->trashed())
			return false;

		$participation = $message->conversation
			->participations
			->where('user_id', $auth_user->id)
			->first();

		if (empty($participation))
			return false;
		/*
				if ($auth_user->id == $message->recepient_id) {
					// сообщение уже удалено
					if ($message->recepient_del)
						return false;

				} elseif ($auth_user->id == $message->sender_id) {
					// сообщение уже удалено
					if ($message->sender_del)
						return false;
				}
				*/
		return true;
	}

	/**
	 * Восстановить сообщение
	 *
	 * @param User $auth_user , Message $message
	 * @return boolean
	 */

	public function restore(User $auth_user, Message $message)
	{
		//if (!$message->isDeletedForUser($auth_user))
		//    return false;

		if (!$message->trashed())
			return false;

		$participation = $message->conversation
			->participations
			->where('user_id', $auth_user->id)
			->first();

		if (empty($participation))
			return false;

		/*
				if ($auth_user->id == $message->recepient_id) {
					// сообщение не удалено
					if (!$message->recepient_del)
						return false;

				} elseif ($auth_user->id == $message->sender_id) {
					// сообщение не удалено
					if (!$message->sender_del)
						return false;
				}
		*/
		return true;
	}
}
