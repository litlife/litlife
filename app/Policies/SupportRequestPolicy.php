<?php

namespace App\Policies;

use App\SupportRequest;
use App\User;

class SupportRequestPolicy extends Policy
{
	/**
	 * Может ли пользователь просмотреть список всех запросов в поддержку
	 *
	 * @param User $auth_user
	 * @param SupportRequest $request
	 * @return bool
	 */
	public function index(User $auth_user)
	{
		return $auth_user->getPermission('reply_to_support_service');
	}

	/**
	 * Может ли пользователь создать сообщение в запросе
	 *
	 * @param User $auth_user
	 * @param SupportRequest $request
	 * @return bool
	 */
	public function createMessage(User $auth_user, SupportRequest $request)
	{
		if ($auth_user->getPermission('reply_to_support_service'))
			return true;

		if ($request->isUserCreator($auth_user))
			return true;

		return false;
	}

	/**
	 * Может ли пользователь просмотреть запрос в поддержку
	 *
	 * @param User $auth_user
	 * @param SupportRequest $request
	 * @return bool
	 */
	public function show(User $auth_user, SupportRequest $request)
	{
		if ($auth_user->getPermission('reply_to_support_service'))
			return true;

		if ($request->isUserCreator($auth_user))
			return true;

		return false;
	}

	/**
	 * Может ли пользователь отправить вопрос в поддержку
	 *
	 * @param User $auth_user
	 * @param SupportRequest $request
	 * @return bool
	 */
	public function create(User $auth_user)
	{
		return $auth_user->getPermission('send_a_support_request');
	}
}
