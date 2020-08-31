<?php

namespace App\Policies;

use App\Keyword;
use App\User;

class KeywordPolicy extends Policy
{
	/**
	 * Может ли пользователь добавить новое ключевое слово
	 *
	 * @param User $auth_user
	 * @return bool
	 */
	public function create(User $auth_user)
	{
		return @(boolean)$auth_user->getPermission('book_keyword_moderate');
	}

	/**
	 * Может ли пользователь отредактировать ключевое слово
	 *
	 * @param User $auth_user
	 * @param Keyword $keyword
	 * @return bool
	 */
	public function update(User $auth_user, Keyword $keyword)
	{
		return @(boolean)$auth_user->getPermission('book_keyword_edit');
	}

	/**
	 * Может ли пользователь удалить ключевое слово
	 *
	 * @param User $auth_user
	 * @param Keyword $keyword
	 * @return bool
	 */
	public function delete(User $auth_user, Keyword $keyword)
	{
		if ($keyword->trashed())
			// ключевое слово уже удалено
			return false;

		return @(boolean)$auth_user->getPermission('book_keyword_remove');
	}

	/**
	 * Может ли пользователь восстановить ключевое слово
	 *
	 * @param User $auth_user
	 * @param Keyword $keyword
	 * @return bool
	 */
	public function restore(User $auth_user, Keyword $keyword)
	{
		if (!$keyword->trashed())
			// ключевое слово уже удалено
			return false;

		return @(boolean)$auth_user->getPermission('book_keyword_remove');
	}

	/**
	 * Может ли пользователь просмотреть список ключевых слов
	 *
	 * @param User $auth_user
	 * @return bool
	 */
	public function view_index(User $auth_user)
	{
		return true;
	}

	/**
	 * Отображать ли кнопку на сайдбаре
	 *
	 * @param User $auth_user
	 * @return bool
	 */
	public function viewAtSidebar(User $auth_user)
	{
		return @(boolean)$auth_user->getPermission('book_keyword_edit');
	}
}
