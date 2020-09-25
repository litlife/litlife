<?php

namespace App\Policies;

use App\Attachment;
use App\User;

class AttachmentPolicy extends Policy
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

	public function delete(User $auth_user, Attachment $attachment)
	{
		// сообщение уже удалено
		if ($attachment->trashed())
			return false;

		if (!$attachment->book->isCanChange($auth_user))
			return false;

		if ($attachment->book->isForSale() and $attachment->isCover())
			return false;

		if ($attachment->book->isPrivate()) {
			if ($attachment->book->isUserCreator($auth_user))
				return true;
		}

		if (optional($attachment->book->getManagerAssociatedWithUser($auth_user))->character == 'author') {
			if (!$attachment->book->isEditionDetailsFilled())
				return true;
		}

		if ($attachment->book->isUserCreator($auth_user))
			return (boolean)$auth_user->getPermission('edit_self_book');
		else
			return (boolean)$auth_user->getPermission('edit_other_user_book');
	}

	public function restore(User $auth_user, Attachment $attachment)
	{
		// сообщение не удалено
		if (!$attachment->trashed())
			return false;

		if (!$attachment->book->isCanChange($auth_user))
			return false;

		if ($attachment->book->isPrivate()) {
			if ($attachment->book->isUserCreator($auth_user))
				return true;
		}

		if (optional($attachment->book->getManagerAssociatedWithUser($auth_user))->character == 'author') {
			if ($attachment->book->isUserCreator($auth_user))
				return true;
		}

		if ($attachment->book->isUserCreator($auth_user))
			return (boolean)$auth_user->getPermission('edit_self_book');
		else
			return (boolean)$auth_user->getPermission('edit_other_user_book');
	}

	/**
	 * Сделать вложение обложкой книги
	 *
	 * @param User $auth_user
	 * @param Attachment $attachment
	 * @return boolean
	 */
	public function setAsCover(User $auth_user, Attachment $attachment)
	{
		if ($attachment->isCover())
			return false;

		if (!$attachment->book->isCanChange($auth_user))
			return false;

		if ($attachment->book->isPrivate()) {
			if ($attachment->book->isUserCreator($auth_user))
				return true;
		}

		if (optional($attachment->book->getManagerAssociatedWithUser($auth_user))->character == 'author') {
			if (!$attachment->book->isEditionDetailsFilled())
				return true;
		}

		if ($attachment->book->isUserCreator($auth_user))
			return (boolean)$auth_user->getPermission('edit_self_book');
		else
			return (boolean)$auth_user->getPermission('edit_other_user_book');
	}
}
