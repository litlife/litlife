<?php

namespace App\Policies;

use App\Section;
use App\User;
use Illuminate\Auth\Access\AuthorizationException;

class SectionPolicy extends Policy
{
    /**
     * Можно ли пользователю просмотреть главу или сноску
     *
     * @param  User  $auth_user
     * @param  Section  $section
     * @throws AuthorizationException
     */
    public function view(?User $auth_user, Section $section)
    {
        if ($section->book->trashed()) {

            if (!empty($auth_user)) {
                if ($section->book->purchases->where('buyer_user_id', $auth_user->id)->first()) {
                    return true;
                }
            }

            return $this->deny(__('book.book_deleted'));
        }

        if ($section->book->isPrivate()) {
            if (empty($auth_user)) {
                return false;
            }

            if ($section->book->isUserCreator($auth_user)) {
                return true;
            }

            return false;
        }

        if ($section->isPrivate()) {

            if (!empty($auth_user)) {
                if (optional($section->book->getManagerAssociatedWithUser($auth_user))->character == 'author') {
                    return true;
                }
            }

            return $this->deny(__('section.access_is_limited'));
        }

        if (!$section->book->isReadAccess()) {

            if (!empty($auth_user)) {
                if (@$auth_user->getPermission('access_to_closed_books')) {
                    return true;
                }
            }

            return $this->deny(__('book.book_closed_for_read'));
        }

        if ($section->book->isForSale()) {

            if ($section->book->free_sections_count > 0) {

                if (!$section->isPaid()) {
                    return true;
                }
            }

            if (!empty($auth_user)) {

                if (!empty($auth_user->getPermission('access_to_closed_books'))) {
                    return true;
                }

                if (optional($section->book->getManagerAssociatedWithUser($auth_user))->character == 'author') {
                    return true;
                }

                if ($section->book->purchases->where('buyer_user_id', $auth_user->id)->first()) {
                    return true;
                }
            }

            return $this->deny(__('book.paid_part_of_book'));
        }

        if ($section->book->isRejected())
        {
            if (!empty($auth_user)) {
                if ($section->book->purchases->where('buyer_user_id', $auth_user->id)->first()) {
                    return true;
                }
            }

            return false;
        }

        return true;
    }

    /**
     * Можно ли пользователю отредактировать главу или сноску
     *
     * @param  User  $auth_user
     * @param  Section  $section
     */
    public function update(User $auth_user, Section $section)
    {
        if (!$section->book->isCanChange($auth_user)) {
            return false;
        }

        if (optional($section->book->getManagerAssociatedWithUser($auth_user))->character == 'author') {
            if (!$section->book->isEditionDetailsFilled()) {
                return true;
            }
        }

        // книга в личном облаке
        if ($section->book->isPrivate()) {
            // разрешаем только если пользователь создатель
            if ($section->book->isUserCreator($auth_user)) {
                return true;
            }
        }

        if ($section->book->isUserCreator($auth_user)) {
            return (boolean) $auth_user->getPermission('edit_self_book');
        } else {
            return (boolean) $auth_user->getPermission('edit_other_user_book');
        }
    }

    /**
     * Можно ли пользователю удалить главу или сноску
     *
     * @param  User  $auth_user
     * @param  Section  $section
     */
    public function delete(User $auth_user, Section $section)
    {
        if ($section->trashed()) {
            return false;
        }

        if (!$section->book->isCanChange($auth_user)) {
            return false;
        }

        if ($section->book->isPrivate()) {
            if ($section->book->isUserCreator($auth_user)) {
                return true;
            }
        }

        if (optional($section->book->getManagerAssociatedWithUser($auth_user))->character == 'author') {
            if (!$section->book->isEditionDetailsFilled()) {
                return true;
            }
        }

        if ($section->book->isUserCreator($auth_user)) {
            return (boolean) $auth_user->getPermission('edit_self_book');
        } else {
            return (boolean) $auth_user->getPermission('edit_other_user_book');
        }
    }

    /**
     * Можно ли пользователю восстановить главу или сноску
     *
     * @param  User  $auth_user
     * @param  Section  $section
     */
    public function restore(User $auth_user, Section $section)
    {
        if (!$section->trashed()) {
            return false;
        }

        if (!$section->book->isCanChange($auth_user)) {
            return false;
        }

        if ($section->book->isPrivate()) {
            if ($section->book->isUserCreator($auth_user)) {
                return true;
            }
        }

        if (optional($section->book->getManagerAssociatedWithUser($auth_user))->character == 'author') {
            if ($section->book->isUserCreator($auth_user)) {
                return true;
            }
        }

        if ($section->book->isUserCreator($auth_user)) {
            return (boolean) $auth_user->getPermission('edit_self_book');
        } else {
            return (boolean) $auth_user->getPermission('edit_other_user_book');
        }
    }

    /**
     * Можно ли пользователю переместить главу или сноску
     *
     * @param  User  $auth_user
     * @param  Section  $section
     */
    public function move(User $auth_user, Section $section)
    {
        if (!$section->book->isCanChange($auth_user)) {
            return false;
        }

        if (optional($section->book->getManagerAssociatedWithUser($auth_user))->character == 'author') {
            if ($section->book->isUserCreator($auth_user)) {
                return true;
            }
        }

        if ($section->book->isUserCreator($auth_user)) {
            return (boolean) $auth_user->getPermission('edit_self_book');
        } else {
            return (boolean) $auth_user->getPermission('edit_other_user_book');
        }
    }

    /**
     * Можно ли пользователю использовать для главы функцию черновика
     *
     * @param  User  $auth_user
     * @param  Section  $section
     */
    public function use_draft(User $auth_user, Section $section)
    {
        if (!$section->isSection()) {
            return false;
        }

        if (optional($section->book->getManagerAssociatedWithUser($auth_user))->character != 'author') {
            return false;
        }

        return true;
    }

}
