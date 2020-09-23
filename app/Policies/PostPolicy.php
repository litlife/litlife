<?php

namespace App\Policies;

use App\Post;
use App\User;

class PostPolicy extends Policy
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
	 * Можно ли пользователю ответить на сообщение на форуме
	 *
	 * @param User $auth_user
	 * @param Post $post
	 * @return boolean
	 */
	public function reply(User $auth_user, Post $post)
	{
		if ($post->isUserCreator($auth_user))
			return false;

		if ($post->isSentForReview())
			return false;

		return true;
	}

	/**
	 * Можно ли пользователю отредактировать пост
	 *
	 * @param User $auth_user
	 * @param Post $post
	 * @return boolean
	 */
	public function update(User $auth_user, Post $post)
	{
		if ($post->isUserCreator($auth_user)) {
			if (@(boolean)$auth_user->getPermission('forum_edit_self_post'))
				return true;

			if (@(boolean)$auth_user->getPermission('forum_edit_self_post_only_time')) {
				// проверяем, прошла ли неделя с даты создания сообщения
				if ($post->created_at->addWeek() > now()) {
					return true;
				}
			}

		} else {
			return (boolean)$auth_user->getPermission('forum_edit_other_user_post');
		}
	}

	/**
	 * Можно ли пользователю удалить пост
	 *
	 * @param User $auth_user
	 * @param Post $post
	 * @return boolean
	 * @throws
	 */
	public function delete(User $auth_user, Post $post)
	{
		// сообщение уже удалено
		if ($post->trashed())
			return false;

		if ($post->isFixed())
			$this->deny(__('post.to_delete_a_message_you_must_first_unpin_it'));

		if ($post->isUserCreator($auth_user))
			return (boolean)$auth_user->getPermission('forum_delete_self_post');
		else
			return (boolean)$auth_user->getPermission('forum_delete_other_user_post');
	}

	/**
	 * Можно ли пользователю восстановить пост
	 *
	 * @param User $auth_user
	 * @param Post $post
	 * @return boolean
	 */
	public function restore(User $auth_user, Post $post)
	{
		// сообщение не удалено
		if (!$post->trashed())
			return false;

		if ($post->isUserCreator($auth_user)) {
			return (boolean)$auth_user->getPermission('forum_delete_self_post');
		} else {
			return (boolean)$auth_user->getPermission('forum_delete_other_user_post');
		}
	}

	/**
	 * Можно ли пользователю переместить пост
	 *
	 * @param User $auth_user
	 * @return boolean
	 */
	public function move(User $auth_user)
	{
		return (boolean)$auth_user->getPermission('forum_move_post');
	}

	/**
	 * Можно ли пользователю закрепить пост
	 *
	 * @param User $auth_user
	 * @param Post $post
	 * @return boolean
	 */
	public function fix(User $auth_user, Post $post)
	{
		if ($post->level > 0)
			return false;

		if ($post->isFixed())
			return false;

		return (boolean)$auth_user->getPermission('forum_post_manage');
	}

	/**
	 * Можно ли пользователю открепить пост
	 *
	 * @param User $auth_user
	 * @param Post $post
	 * @return boolean
	 */
	public function unfix(User $auth_user, Post $post)
	{
		if (!$post->isFixed())
			return false;

		return (boolean)$auth_user->getPermission('forum_post_manage');
	}

	/**
	 * Можно ли пользователю обобрить комментарий на проверке
	 *
	 * @param User $auth_user
	 * @param Post $post
	 * @return boolean
	 */
	public function approve(User $auth_user, Post $post)
	{
		if (!$post->isSentForReview())
			return false;

		return @(boolean)$auth_user->getPermission('check_post_comments');
	}

	/**
	 * Можно ли пользователю просматривать посты на проверке
	 *
	 * @param User $auth_user
	 * @return boolean
	 */
	public function viewOnCheck(User $auth_user)
	{
		return @(boolean)$auth_user->getPermission('check_post_comments');
	}

	/**
	 * Можно ли просмотреть данные об устройстве
	 *
	 * @param User $auth_user
	 * @param Post $post
	 * @return boolean
	 */
	public function see_technical_information(User $auth_user, Post $post)
	{
		if (empty($post->user_agent_id))
			return false;

		return (boolean)$auth_user->getPermission('display_technical_information');
	}

	/**
	 * Может ли пользователь пожаловаться на сообщение на форуме
	 *
	 * @param User $auth_user
	 * @param Post $post
	 * @return bool
	 */
	public function complain(User $auth_user, Post $post)
	{
		if ($post->trashed())
			return false;

		return (boolean)$auth_user->getPermission('Complain');
	}
}
