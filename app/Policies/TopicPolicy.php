<?php

namespace App\Policies;

use App\Topic;
use App\User;

class TopicPolicy extends Policy
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
	 * Можно ли пользователю создать пост в теме
	 *
	 * @param User $auth_user
	 * @param Topic $topic
	 * @return boolean
	 */
	public function create_post(User $auth_user, Topic $topic)
	{
		if ($topic->isClosed())
			return false;

		if ($topic->isArchived())
			return false;

		if ($topic->trashed())
			return false;

		return (boolean)$auth_user->getPermission('add_forum_post');
	}

	public function create(User $auth_user, Topic $topic)
	{
		return (boolean)$auth_user->getPermission('add_forum_topic');
	}

	public function update(User $auth_user, Topic $topic)
	{
		if ($topic->isUserCreator($auth_user))
			return (boolean)$auth_user->getPermission('edit_forum_self_topic');
		else
			return (boolean)$auth_user->getPermission('edit_forum_other_user_topic');
	}

	public function delete(User $auth_user, Topic $topic)
	{
		// уже удалено
		if ($topic->trashed())
			return false;

		if ($topic->isUserCreator($auth_user))
			return (boolean)$auth_user->getPermission('delete_forum_self_topic');
		else
			return (boolean)$auth_user->getPermission('delete_forum_other_user_topic');
	}

	public function restore(User $auth_user, Topic $topic)
	{
		// не удалено
		if (!$topic->trashed())
			return false;

		if ($topic->isUserCreator($auth_user))
			return (boolean)$auth_user->getPermission('delete_forum_self_topic');
		else
			return (boolean)$auth_user->getPermission('delete_forum_other_user_topic');
	}

	/**
	 * Можно ли пользователю открыть тему
	 *
	 * @param User $auth_user
	 * @param Topic $topic
	 * @return boolean
	 */

	public function open(User $auth_user, Topic $topic)
	{
		if ($topic->isOpened())
			return false;

		return (boolean)$auth_user->getPermission('manipulate_topic');
	}

	/**
	 * Можно ли пользователю закрыть тему
	 *
	 * @param User $auth_user
	 * @param Topic $topic
	 * @return boolean
	 */

	public function close(User $auth_user, Topic $topic)
	{
		if ($topic->isClosed())
			return false;

		return (boolean)$auth_user->getPermission('manipulate_topic');
	}

	/**
	 * Можно ли пользователю объединять темы
	 *
	 * @param User $auth_user
	 * @return boolean
	 */

	public function merge(User $auth_user)
	{
		return (boolean)$auth_user->getPermission('manipulate_topic');
	}

	/**
	 * Можно ли пользователю переместить тему
	 *
	 * @param User $auth_user
	 * @param Topic $topic
	 * @return boolean
	 */

	public function move(User $auth_user, Topic $topic)
	{
		return (boolean)$auth_user->getPermission('manipulate_topic');
	}

	public function archive(User $auth_user, Topic $topic)
	{
		if ($topic->isArchived())
			return false;

		return (boolean)$auth_user->getPermission('manipulate_topic');
	}

	public function unarchive(User $auth_user, Topic $topic)
	{
		if (!$topic->isArchived())
			return false;

		return (boolean)$auth_user->getPermission('manipulate_topic');
	}

	public function edit_spectial_settings(User $auth_user)
	{
		return (boolean)$auth_user->getPermission('manipulate_topic');
	}

	public function subscribe(User $auth_user, Topic $topic)
	{
		$auth_user_topic_subscription = $topic->auth_user_subscription;

		if ($auth_user_topic_subscription)
			$this->deny(__('topic.you_are_already_subscribed_to_this_topic'));

		return true;
	}

	public function unsubscribe(User $auth_user, Topic $topic)
	{
		$auth_user_topic_subscription = $topic->auth_user_subscription;

		if (!$auth_user_topic_subscription)
			$this->deny(__('topic.you_are_not_subscribed_to_this_topic'));

		return true;
	}
}
