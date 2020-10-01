<?php

namespace App\Policies;

use App\Complain;
use App\User;

class ComplainPolicy extends Policy
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
	 * Можно ли пользователю отправлять жалобу
	 *
	 * @param User $auth_user
	 * @return boolean
	 */
	public function create(User $auth_user)
	{
		return (boolean)$auth_user->getPermission('Complain');
	}

	/**
	 * Можно ли пользователю редактировать жалобу
	 *
	 * @param User $auth_user
	 * @param Complain $complain
	 * @return boolean
	 */
	public function update(User $auth_user, Complain $complain)
	{
		if ($complain->isAccepted())
			return false;

		if ($complain->isReviewStarts())
			return false;

		if ($auth_user->is($complain->create_user))
			return $auth_user->getPermission('Complain');

		return false;
	}

	/**
	 * Можно ли пользователю проверять жалобы
	 *
	 * @param User $auth_user
	 * @param Complain $complain
	 * @return boolean
	 */
	public function approve(User $auth_user, Complain $complain)
	{
		if (!$complain->isReviewStarts())
			return false;

		if ($complain->status_changed_user->id != $auth_user->id)
			return false;

		return (boolean)$auth_user->getPermission('complain_check');
	}

	/**
	 * Можно ли пользователю просматривать жалобу
	 *
	 * @param User $auth_user
	 * @param Complain $complain
	 * @return boolean
	 */
	public function view(User $auth_user, Complain $complain)
	{
		if ($complain->isUserCreator($auth_user))
			return true;

		return (boolean)$auth_user->getPermission('complain_check');
	}

	/**
	 * Можно ли пользователю начать рассматривать жалобу
	 *
	 * @param User $auth_user
	 * @param Complain $complain
	 * @return boolean
	 */
	public function startReview(User $auth_user, Complain $complain)
	{
		if (!$complain->isSentForReview())
			return false;

		return (boolean)$auth_user->getPermission('complain_check');
	}

	/**
	 * Можно ли пользователю прекратить рассматривать жалобу
	 *
	 * @param User $auth_user
	 * @param Complain $complain
	 * @return boolean
	 */
	public function stopReview(User $auth_user, Complain $complain)
	{
		if (!$complain->isReviewStarts())
			return false;

		if ($complain->status_changed_user->id != $auth_user->id)
			return false;

		return (boolean)$auth_user->getPermission('complain_check');
	}

	/**
	 * Можно ли пользователю просматривать список жалоб на рассмотрении
	 *
	 * @param User $auth_user
	 * @return boolean
	 */
	public function viewOnReviewList(User $auth_user)
	{
		return (boolean)$auth_user->getPermission('complain_check');
	}
}
