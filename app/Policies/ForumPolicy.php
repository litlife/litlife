<?php

namespace App\Policies;

use App\Forum;
use App\User;

class ForumPolicy extends Policy
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
	 * Может ли пользователь создать на форуме тему
	 *
	 * @param User $auth_user
	 * @param Forum $forum
	 * @return bool
	 */

	public function create_topic(User $auth_user, Forum $forum)
	{
		if ($auth_user->forum_message_count < $forum->min_message_count)
			return false;

		return (boolean)$auth_user->getPermission('add_forum_topic');
	}

	/**
	 * Можно ли пользователю просмотреть форум
	 *
	 * @param User $auth_user
	 * @return boolean
	 */

	public function view(?User $auth_user, Forum $forum)
	{
		if (!$forum->isPrivate())
			return true;
		else {
			if (empty($auth_user))
				return false;
			else
				return $forum->user_access->where('user_id', $auth_user->id)->first() ? true : false;
		}
	}

	public function create(User $auth_user)
	{
		return (boolean)$auth_user->getPermission('add_forum_forum');
	}

	public function update(User $auth_user, Forum $forum)
	{
		return (boolean)$auth_user->getPermission('forum_edit_forum');
	}

	public function delete(User $auth_user, Forum $forum)
	{
		// уже удалено
		if ($forum->trashed())
			return false;

		return (boolean)$auth_user->getPermission('delete_forum_forum');
	}

	public function restore(User $auth_user, Forum $forum)
	{
		// не удалено
		if (!$forum->trashed())
			return false;

		return (boolean)$auth_user->getPermission('delete_forum_forum');
	}

	/**
	 * Можно ли пользователю изменить порядок форумов
	 *
	 * @param User $auth_user
	 * @return boolean
	 */

	public function change_order(User $auth_user)
	{
		return (boolean)$auth_user->getPermission('forum_list_manipulate');
	}
}
