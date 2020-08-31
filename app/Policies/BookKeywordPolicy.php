<?php

namespace App\Policies;

use App\BookKeyword;
use App\User;

class BookKeywordPolicy extends Policy
{
	/**
	 * Может ли пользователь одобрить ключевое слово на проверке
	 *
	 * @param User $auth_user
	 * @param BookKeyword $keyword
	 * @return bool
	 */
	public function approve(User $auth_user, BookKeyword $keyword)
	{
		if (!$keyword->isSentForReview())
			// ключевое слово не на проверке
			return false;

		if (!@$auth_user->getPermission('BookKeywordModerate'))
			return false;

		return true;
	}

	/**
	 * Может ли пользователь удалить ключевое слово
	 *
	 * @param User $auth_user
	 * @param BookKeyword $keyword
	 * @return bool
	 */
	public function delete(User $auth_user, BookKeyword $keyword)
	{
		if ($keyword->trashed())
			// ключевое слово уже удалено
			return false;

		if (empty($keyword->book))
			return false;

		if ($keyword->book->isPrivate()) {
			if ($keyword->book->isUserCreator($auth_user))
				return true;
		}

		if ($keyword->isSentForReview() or $keyword->isPrivate()) {
			if ($keyword->isUserCreator($auth_user))
				// пользователь может удалять свои еще не проверенные ключевые слова
				return true;
		} else {
			if ($keyword->book->getManagerAssociatedWithUser($auth_user))
				return true;
		}

		// проверяем может ли он удалять ключевые слова
		return @(boolean)$auth_user->getPermission('book_keyword_remove');
	}

	/**
	 * Может ли пользователь восстановить ключевое слово
	 *
	 * @param User $auth_user
	 * @param BookKeyword $keyword
	 * @return bool
	 */
	public function restore(User $auth_user, BookKeyword $keyword)
	{
		if (!$keyword->trashed())
			// ключевое слово уже удалено
			return false;

		if (empty($keyword->book))
			return false;

		if ($keyword->book->isPrivate()) {
			if ($keyword->book->isUserCreator($auth_user))
				return true;
		}

		if ($keyword->isSentForReview() or $keyword->isPrivate()) {
			if ($keyword->isUserCreator($auth_user))
				// пользователь может удалять свои еще не проверенные ключевые слова
				return true;
		} else {
			if ($keyword->book->getManagerAssociatedWithUser($auth_user))
				return true;
		}

		// проверяем может ли он удалять ключевые слова
		return @(boolean)$auth_user->getPermission('book_keyword_remove');
	}

	/**
	 * Может ли пользователь поставить голос за ключевое слово
	 *
	 * @param User $auth_user
	 * @return bool
	 */
	public function vote(User $auth_user)
	{
		return @(boolean)$auth_user->getPermission('BookKeywordVote');
	}

	/**
	 * Может ли пользователь просматривать ключевые слова на модерации
	 *
	 * @param User $auth_user
	 * @return bool
	 */
	public function viewOnCheck(User $auth_user)
	{
		return @(boolean)$auth_user->getPermission('BookKeywordModerate');
	}
}
