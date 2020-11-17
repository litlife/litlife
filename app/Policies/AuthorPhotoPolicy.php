<?php

namespace App\Policies;

use App\AuthorPhoto;
use App\User;

class AuthorPhotoPolicy extends Policy
{
    public function delete(User $auth_user, AuthorPhoto $photo)
    {
        if ($photo->trashed()) // автор уже удален
        {
            return false;
        }

        if (empty($photo->author)) {
            return false;
        }

        // автор в личной библиотеке
        if ($photo->author->isPrivate()) {
            // разрешаем только если пользователь создатель
            if ($photo->author->isUserCreator($auth_user)) {
                return true;
            }
        } else {
            // иначе автор может быть на модерации или в общей библиотеке
            if ($auth_user->can('manage', $photo->author)) {
                return true;
            }

            return (boolean) $auth_user->getPermission('author_edit');
        }
    }

    public function restore(User $auth_user, AuthorPhoto $photo)
    {
        if (!$photo->trashed()) // автор не удален
        {
            return false;
        }

        // автор в личном облаке
        if ($photo->author->isPrivate()) {
            // разрешаем только если пользователь создатель
            if ($photo->author->isUserCreator($auth_user)) {
                return true;
            }
        } else {
            // иначе автор может быть на модерации или в общей библиотеке
            if ($auth_user->can('manage', $photo->author)) {
                return true;
            }

            return (boolean) $auth_user->getPermission('author_edit');
        }
    }
}
