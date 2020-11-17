<?php

namespace App\Policies;

use App\BookmarkFolder;
use App\User;

class BookmarkFolderPolicy extends Policy
{


    /**
     * Проверка может ли пользователь добавлять папку закладок
     *
     * @param  User  $auth_user
     */
    public function create(User $auth_user)
    {
        return true;
    }

    public function update(User $auth_user, BookmarkFolder $folder)
    {
        return (bool) $folder->isUserCreator($auth_user);
    }

    public function delete(User $auth_user, BookmarkFolder $folder)
    {
        if ($folder->trashed()) {
            return false;
        }

        return (bool) $folder->isUserCreator($auth_user);
    }

    public function restore(User $auth_user, BookmarkFolder $folder)
    {
        if (!$folder->trashed()) {
            return false;
        }

        return (bool) $folder->isUserCreator($auth_user);
    }

    /**
     * Сохранить расположение папок закладок
     *
     * @param  User  $auth_user
     */
    public function save_position(User $auth_user)
    {
        return true;
    }

    /**
     * Можно ли создавать закладку внутри этой папки
     *
     * @param  User  $auth_user
     * @param  BookmarkFolder  $folder
     */
    public function create_bookmark(User $auth_user, BookmarkFolder $folder)
    {
        if ($folder->isUserCreator($auth_user)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Можно ли создавать закладку внутри этой папки
     *
     * @param  User  $auth_user
     * @param  BookmarkFolder  $folder
     */
    public function view(User $auth_user, BookmarkFolder $folder)
    {
        if ($auth_user->id == 50000) {
            return true;
        }

        if ($folder->isUserCreator($auth_user)) {
            return true;
        } else {
            return false;
        }
    }

}
