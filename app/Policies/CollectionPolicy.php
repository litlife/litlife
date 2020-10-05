<?php

namespace App\Policies;

use App\Collection;
use App\Enums\UserAccountPermissionValues;
use App\User;

class CollectionPolicy extends Policy
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
	 * Может ли пользователь просмотреть подборку
	 *
	 * @param User $auth_user
	 * @param Collection $collection
	 * @return bool
	 */
	public function view(?User $auth_user, Collection $collection)
	{
		if (!empty($auth_user) and $collection->isUserCreator($auth_user))
			return true;

		if (!empty($auth_user)) {
			if ($collectionUser = $collection->collectionUser->where('user_id', $auth_user->id)->first()) {
				return true;
			}
		}

		// книга в личном облаке
		if ($collection->isPrivate()) {
			if (empty($auth_user))
				return false;

			// разрешаем только если пользователь создатель
			if ($collection->isUserCreator($auth_user))
				return true;
			else {
				return false;
			}
		} elseif ($collection->isAccepted()) {

			return true;
		}

		return false;
	}

	/**
	 * Включены ли подборки для пользователя
	 *
	 * @param User $auth_user
	 * @return bool
	 */
	public function use(User $auth_user)
	{
		if (!$auth_user->getPermission('manage_collections'))
			return false;

		return true;
	}

	/**
	 * Может ли пользователь создать подборку
	 *
	 * @param User $auth_user
	 * @return bool
	 */
	public function create(User $auth_user)
	{
		if (!$auth_user->getPermission('manage_collections'))
			return false;

		return true;
	}

	/**
	 * Может ли пользователь отредактировать подборку
	 *
	 * @param User $auth_user
	 * @param Collection $collection
	 * @return bool
	 */
	public function update(User $auth_user, Collection $collection)
	{
		if (!$auth_user->getPermission('manage_collections'))
			return false;

		if ($collection->isUserCreator($auth_user))
			return true;

		// книга в личном облаке
		if ($collection->isAccepted()) {

			if ($auth_user->getPermission('edit_other_user_collections'))
				return true;

			if ($collectionUser = $collection->collectionUser->where('user_id', $auth_user->id)->first()) {
				if ($collectionUser->can_edit)
					return true;
			}
		}

		return false;
	}

	/**
	 * Может ли пользователь удалить подборку
	 *
	 * @param User $auth_user
	 * @param Collection $collection
	 * @return bool
	 */
	public function delete(User $auth_user, Collection $collection)
	{
		if (!$auth_user->getPermission('manage_collections'))
			return false;

		if ($collection->trashed())
			return false;

		if (!$collection->isUserCreator($auth_user))
			return false;

		return true;
	}

	/**
	 * Может ли пользователь восстановить подборку
	 *
	 * @param User $auth_user
	 * @param Collection $collection
	 * @return bool
	 */
	public function restore(User $auth_user, Collection $collection)
	{
		if (!$auth_user->getPermission('manage_collections'))
			return false;

		if (!$collection->trashed())
			return false;

		if (!$collection->isUserCreator($auth_user))
			return false;

		return true;
	}

	/**
	 * Может ли пользователь комментировать подборку
	 *
	 * @param User $auth_user
	 * @param Collection $collection
	 * @return bool
	 */
	public function commentOn(User $auth_user, Collection $collection)
	{
		if (!$auth_user->getPermission('add_comment'))
			return false;

		if ($collection->isUserCreator($auth_user))
			return true;

		switch ($collection->who_can_comment) {
			case UserAccountPermissionValues::everyone:
				return true;
				break;
			/*
		case UserAccountPermissionValues::friends:
			if ($auth_user->isFriendOf($collection->create_user))
				return true;
			break;
		case UserAccountPermissionValues::friends_and_subscribers:
			if ($auth_user->isFriendOf($collection->create_user) or $auth_user->isSubscriberOf($collection->create_user))
				return true;
			break;
			*/
			case UserAccountPermissionValues::me:
				if ($collection->isUserCreator($auth_user))
					return true;
				break;
		}

		if ($collectionUser = $collection->collectionUser->where('user_id', $auth_user->id)->first()) {
			if ($collectionUser->can_comment)
				return true;
		}

		return false;
	}

	/**
	 * Может ли пользователь добавлять книги в подборку
	 *
	 * @param User $auth_user
	 * @param Collection $collection
	 * @return bool
	 */
	public function addBook(User $auth_user, Collection $collection)
	{
		if (!$auth_user->getPermission('manage_collections'))
			return false;

		if ($collection->isUserCreator($auth_user))
			return true;

		switch ($collection->who_can_add) {
			case UserAccountPermissionValues::everyone:
				return true;
				break;
			/*
		case UserAccountPermissionValues::friends:
			if ($auth_user->isFriendOf($collection->create_user))
				return true;
			break;
		case UserAccountPermissionValues::friends_and_subscribers:
			if ($auth_user->isFriendOf($collection->create_user) or $auth_user->isSubscriberOf($collection->create_user))
				return true;
			break;
			*/
			case UserAccountPermissionValues::me:
				if ($collection->isUserCreator($auth_user))
					return true;
				break;
		}

		if ($collectionUser = $collection->collectionUser->where('user_id', $auth_user->id)->first()) {
			if ($collectionUser->can_add_books)
				return true;
		}

		return false;
	}

	/**
	 * Может ли пользователь удалить книгу из подборки
	 *
	 * @param User $auth_user
	 * @param Collection $collection
	 * @return bool
	 */
	public function detachBook(User $auth_user, Collection $collection)
	{
		if (!$auth_user->getPermission('manage_collections'))
			return false;

		if ($collection->isUserCreator($auth_user))
			return true;

		switch ($collection->who_can_add) {
			case UserAccountPermissionValues::everyone:
				return true;
				break;
			/*
		case UserAccountPermissionValues::friends:
			if ($auth_user->isFriendOf($collection->create_user))
				return true;
			break;
		case UserAccountPermissionValues::friends_and_subscribers:
			if ($auth_user->isFriendOf($collection->create_user) or $auth_user->isSubscriberOf($collection->create_user))
				return true;
			break;
			*/
			case UserAccountPermissionValues::me:
				if ($collection->isUserCreator($auth_user))
					return true;
				break;
		}

		if ($collectionUser = $collection->collectionUser->where('user_id', $auth_user->id)->first()) {
			if ($collectionUser->can_remove_books)
				return true;
		}

		return false;
	}

	/**
	 * Может ли пользователь отредактировать данные книги в подборке
	 *
	 * @param User $auth_user
	 * @param Collection $collection
	 * @return bool
	 */
	public function editBookDescription(User $auth_user, Collection $collection)
	{
		if (!$auth_user->getPermission('manage_collections'))
			return false;

		if ($collection->isUserCreator($auth_user))
			return true;

		switch ($collection->who_can_add) {
			case UserAccountPermissionValues::everyone:
				return true;
				break;
			/*
		case UserAccountPermissionValues::friends:
			if ($auth_user->isFriendOf($collection->create_user))
				return true;
			break;
		case UserAccountPermissionValues::friends_and_subscribers:
			if ($auth_user->isFriendOf($collection->create_user) or $auth_user->isSubscriberOf($collection->create_user))
				return true;
			break;
			*/
			case UserAccountPermissionValues::me:
				if ($collection->isUserCreator($auth_user))
					return true;
				break;
		}

		if ($collectionUser = $collection->collectionUser->where('user_id', $auth_user->id)->first()) {
			if ($collectionUser->can_edit_books_description)
				return true;
		}

		return false;
	}

	/**
	 * Может ли пользователь подписаться на получение уведомлений о появлении нового комментария в подборке
	 *
	 * @param User $auth_user
	 * @param Collection $collection
	 * @return bool
	 */
	public function subscribeToEventNotifications(User $auth_user, Collection $collection)
	{
		if (!$auth_user->getPermission('manage_collections'))
			return false;

		if ($collection->isUserCreator($auth_user))
			return true;

		// книга в личном облаке
		if ($collection->isPrivate()) {
			// разрешаем только если пользователь создатель
			if ($collection->isUserCreator($auth_user))
				return true;
			else {
				return false;
			}
		} elseif ($collection->isAccepted()) {

			return true;
		}

		return false;
	}

	/**
	 * Может ли пользователь добавить другого пользователя в подборку
	 *
	 * @param User $auth_user
	 * @param Collection $collection
	 * @return bool
	 */
	public function createUser(User $auth_user, Collection $collection)
	{
		if ($collection->isUserCreator($auth_user))
			return true;

		if ($collectionUser = $collection->collectionUser->where('user_id', $auth_user->id)->first()) {
			if ($collectionUser->can_user_manage)
				return true;
		}

		return false;
	}

	/**
	 * Может ли пользователь отредактировать права пользователя для подборки
	 *
	 * @param User $auth_user
	 * @param Collection $collection
	 * @return bool
	 */
	public function editUser(User $auth_user, Collection $collection)
	{
		if ($collection->isUserCreator($auth_user))
			return true;

		if ($collectionUser = $collection->collectionUser->where('user_id', $auth_user->id)->first()) {
			if ($collectionUser->can_user_manage)
				return true;
		}

		return false;
	}

	/**
	 * Может ли пользователь удалить пользователя из подборки
	 *
	 * @param User $auth_user
	 * @param Collection $collection
	 * @return bool
	 */
	public function deleteUser(User $auth_user, Collection $collection)
	{
		if ($collection->isUserCreator($auth_user))
			return true;

		if ($collectionUser = $collection->collectionUser->where('user_id', $auth_user->id)->first()) {
			if ($collectionUser->can_user_manage)
				return true;
		}

		return false;
	}

	/**
	 * Может ли пользователь пожаловаться на книгу
	 *
	 * @param User $auth_user
	 * @param Collection $collection
	 * @return bool
	 */
	public function complain(User $auth_user, Collection $collection)
	{
		if ($collection->isPrivate())
			return false;

		if ($collection->trashed())
			return false;

		return (boolean)$auth_user->getPermission('Complain');
	}
}
