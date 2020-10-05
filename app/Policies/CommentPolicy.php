<?php

namespace App\Policies;

use App\Comment;
use App\User;

class CommentPolicy extends Policy
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
	 * Ответить на комментарий
	 *
	 * @param User $auth_user
	 * @param Comment $comment
	 * @return boolean
	 */
	public function reply(User $auth_user, Comment $comment)
	{
		if (empty($comment->create_user))
			return false;

		if ($comment->isSentForReview())
			return false;

		if ($comment->create_user->hasAddedToBlacklist($auth_user))
			$this->deny(__('comment.you_cant_reply_to_a_comment_if_the_user_is_blacklisted'));

		if ($comment->isUserCreator($auth_user))
			$this->deny(__('comment.you_cant_reply_to_your_comments'));
		else
			return true;
	}

	/**
	 * Отредактировать комментарий
	 *
	 * @param User $auth_user
	 * @param Comment $comment
	 * @return boolean
	 */
	public function update(User $auth_user, Comment $comment)
	{
		if ($comment->isPrivate())
			return true;

		if ($comment->isUserCreator($auth_user)) {
			if ($comment->isBookType()) {

				if ($auth_user->getPermission('comment_self_edit_only_time')) {
					if (now()->lessThan($comment->created_at->addSeconds(604800)))
						return true;
				}

				if ($auth_user->getPermission('CommentEditMy'))
					return true;

			} elseif ($comment->isCollectionType()) {
				if ($auth_user->getPermission('edit_or_delete_your_comments_to_collections'))
					return true;
			}
		}

		if ($auth_user->getPermission('CommentEditOtherUser'))
			return true;
		else
			return false;
	}

	/**
	 * Удалить комментарий
	 *
	 * @param User $auth_user
	 * @param Comment $comment
	 * @return boolean
	 */
	public function delete(User $auth_user, Comment $comment)
	{
		if ($comment->isPrivate())
			return true;

		if ($comment->trashed())
			return false;

		if ($comment->isUserCreator($auth_user)) {
			if ($comment->isBookType()) {
				if ($auth_user->getPermission('DeleteMyComment'))
					return true;

			} elseif ($comment->isCollectionType()) {
				if ($auth_user->getPermission('edit_or_delete_your_comments_to_collections'))
					return true;
			}
		}

		return $auth_user->getPermission('DeleteOtherUserComment');
	}

	/**
	 * Восстановить комментарий
	 *
	 * @param User $auth_user
	 * @param Comment $comment
	 * @return boolean
	 */
	public function restore(User $auth_user, Comment $comment)
	{
		if ($comment->isPrivate())
			return true;

		if (!$comment->trashed())
			return false;

		if ($comment->isUserCreator($auth_user)) {
			if ($comment->isBookType()) {
				if ($auth_user->getPermission('DeleteMyComment'))
					return true;

			} elseif ($comment->isCollectionType()) {
				if ($auth_user->getPermission('edit_or_delete_your_comments_to_collections'))
					return true;
			}
		}

		return $auth_user->getPermission('DeleteOtherUserComment');
	}

	/**
	 * Просмотреть комментарий
	 *
	 * @param User $auth_user
	 * @param Comment $comment
	 * @return boolean
	 */
	public function view(?User $auth_user, Comment $comment)
	{
		if (!empty($auth_user) and $comment->isUserCreator($auth_user))
			return true;

		if ($comment->isPrivate()) {
			if (!empty($auth_user) and $comment->isUserCreator($auth_user))
				return true;
			else
				return false;
		}

		if ($comment->isCollectionType()) {
			if ($comment->originCommentable->isPrivate()) {
				if ($comment->originCommentable->isUserCreator($auth_user))
					return true;

				if ($collectionUser = $comment->originCommentable->collectionUser->where('user_id', $auth_user->id)->first())
					return true;
				else
					return false;
			}
		}

		return true;
	}

	/**
	 * Можно ли голосовать за комментарий
	 *
	 * @param User $auth_user
	 * @param Comment $comment
	 * @return boolean
	 */
	public function vote(User $auth_user, Comment $comment)
	{
		if (empty($comment->create_user))
			return false;
		/*
				if ($comment->isUserCreator($auth_user))
					$this->deny(__('comment.you_cant_vote_for_your_comments'));
		*/
		if ($comment->create_user->hasAddedToBlacklist($auth_user))
			$this->deny(__('comment.you_cant_vote_for_a_comment_if_the_user_is_blacklisted'));

		return (boolean)$auth_user->getPermission('CommentAddVote');
	}

	/**
	 * Просматривать кому понравился или не понравился комментарий
	 *
	 * @param User $auth_user
	 * @param Comment $comment
	 * @return boolean
	 */
	public function viewWhoLikesOrDislikes(User $auth_user, Comment $comment)
	{
		return @(boolean)$auth_user->getPermission('comment_view_who_likes_or_dislikes');
	}

	/**
	 * Можно ли одобрить комментарий на проверке
	 *
	 * @param User $auth_user
	 * @param Comment $comment
	 * @return boolean
	 */
	public function approve(User $auth_user, Comment $comment)
	{
		if (!$comment->isSentForReview())
			return false;

		return @(boolean)$auth_user->getPermission('check_post_comments');
	}

	/**
	 * Просматривать комментарии на проверке
	 *
	 * @param User $auth_user
	 * @return boolean
	 */
	public function viewOnCheck(User $auth_user)
	{
		return @(boolean)$auth_user->getPermission('CheckPostComments');
	}

	/**
	 * Можно ли просмотреть данные об устройстве
	 *
	 * @param User $auth_user
	 * @param Comment $comment
	 * @return boolean
	 */
	public function see_technical_information(User $auth_user, Comment $comment)
	{
		if (empty($comment->user_agent_id))
			return false;

		return (boolean)$auth_user->getPermission('display_technical_information');
	}

	/**
	 * Может ли пользователь пожаловаться на комментарий
	 *
	 * @param User $auth_user
	 * @param Comment $comment
	 * @return bool
	 */
	public function complain(User $auth_user, Comment $comment)
	{
		if ($comment->trashed())
			return false;

		return (boolean)$auth_user->getPermission('Complain');
	}

	/**
	 * Может ли опубликовать комментарий
	 *
	 * @param User $auth_user
	 * @param Comment $comment
	 * @return bool
	 */
	public function publish(User $auth_user, Comment $comment)
	{
		if (!$comment->isUserCreator($auth_user))
			return false;

		if ($comment->trashed())
			return false;

		if ($comment->isPrivate())
			return true;
		else
			return false;
	}
}
