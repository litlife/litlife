<?php

namespace App\Policies;

use App\BookFile;
use App\User;

class BookFilePolicy extends Policy
{
    /**
     * Может ли пользователь редактировать описания файлов книг
     *
     * @param  User  $auth_user
     * @param  BookFile  $file
     */
    public function update(User $auth_user, BookFile $file)
    {
        if (!$file->book->isCanChange($auth_user)) {
            return false;
        }

        if ($file->isPrivate() or $file->isSentForReview()) {
            if ($file->isUserCreator($auth_user)) {
                return true;
            }
        }

        if ($file->book->isPrivate()) {
            if ($file->book->isUserCreator($auth_user)) {
                return true;
            }
        }

        if ($file->book->getManagerAssociatedWithUser($auth_user)) {
            return true;
        }

        return (boolean) $auth_user->getPermission('book_file_edit');
    }

    /**
     * Может ли пользователь удалять файлы книг
     *
     * @param  User  $auth_user
     * @param  BookFile  $file
     */
    public function delete(User $auth_user, BookFile $file)
    {
        if ($file->trashed()) // файл книги уже удален
        {
            return false;
        }

        if (!$file->book->isCanChange($auth_user)) {
            return false;
        }

        // книга еще не распарсена поэтому запрещаем удалять файлы книги
        if (!$file->book->parse->isSucceed() and !$file->book->parse->isFailed()) {
            return false;
        }

        if ($file->book->isPrivate()) {
            if ($file->book->isUserCreator($auth_user)) {
                return true;
            }
        } else {

            if (optional($file->book->getManagerAssociatedWithUser($auth_user))->character == 'author') {
                return true;
            }

            return (boolean) $auth_user->getPermission('book_file_delete');
        }
    }

    /**
     * Может ли пользователь удалять файлы книг
     *
     * @param  User  $auth_user
     * @param  BookFile  $file
     */
    public function restore(User $auth_user, BookFile $file)
    {
        if (!$file->trashed()) // файл книги не удален
        {
            return false;
        }

        if (!$file->book->isCanChange($auth_user)) {
            return false;
        }

        // книга еще не распарсена поэтому запрещаем удалять файлы книги
        if (!$file->book->parse->isSucceed()) {
            return false;
        }

        if ($file->book->isPrivate()) {
            if ($file->book->isUserCreator($auth_user)) {
                return true;
            }
        } else {

            if (optional($file->book->getManagerAssociatedWithUser($auth_user))->character == 'author') {
                return true;
            }

            return (boolean) $auth_user->getPermission('book_file_delete');
        }
    }

    /**
     * Может ли пользователь одобрять файлы книг на проверке
     *
     * @param  User  $auth_user
     * @param  BookFile  $file
     */
    public function approve(User $auth_user, BookFile $file)
    {
        if (!$file->isSentForReview()) {
            return false;
        }

        return (boolean) $auth_user->getPermission('book_file_add_check');
    }

    /**
     * Может ли пользователь отклонять файлы книг на проверке
     *
     * @param  User  $auth_user
     * @param  BookFile  $file
     */
    public function decline(User $auth_user, BookFile $file)
    {
        if (!$file->isSentForReview()) {
            return false;
        }

        return (boolean) $auth_user->getPermission('book_file_add_check');
    }

    /**
     * Может ли пользователь просматривать файлы на проверке
     *
     * @param  User  $auth_user
     */
    public function view_on_moderation(User $auth_user)
    {
        return (boolean) $auth_user->getPermission('book_file_add_check');
    }

    /**
     * Может ли пользователь сделать из файла книги источник и создать новые страницы
     *
     * @param  User  $auth_user
     * @param  BookFile  $file
     */
    public function set_source_and_make_pages(User $auth_user, BookFile $file)
    {
        if (!$file->book->isCanChange($auth_user)) {
            return false;
        }

        if ($file->book->parse->isWait()) {
            return false;
        }

        if ($file->book->parse->isProgress()) {
            return false;
        }

        if (!$file->canParsed()) {
            return false;
        }

        if (!$file->book->parse->isSucceed()) {
            return false;
        }

        if ($file->book->isPrivate()) {
            if ($file->book->isUserCreator($auth_user)) {
                return true;
            }
        } else {

            $book = &$file->book;

            if (optional($book->getManagerAssociatedWithUser($auth_user))->isAuthorCharacter()) {
                if ($book->isEditionDetailsFilled() and !$book->isUserCreator($auth_user)) {
                    return false;
                } else {
                    return true;
                }
            }

            if ($file->book->isUserCreator($auth_user)) {
                return (boolean) $auth_user->getPermission('edit_self_book');
            } else {
                return (boolean) $auth_user->getPermission('edit_other_user_book');
            }
        }
    }
}
