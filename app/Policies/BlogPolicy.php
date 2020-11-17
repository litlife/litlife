<?php

namespace App\Policies;

use App\Blog;
use App\Enums\UserAccountPermissionValues;
use App\User;

class BlogPolicy extends Policy
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
     * Ответить на сообщение в блоге
     * @param  User  $auth_user
     * @param  Blog  $blog
     */
    public function reply(User $auth_user, Blog $blog)
    {
        if ($blog->isSentForReview()) {
            return false;
        }

        if ($blog->isUserCreator($auth_user)) {
            return false;
        }

        if (!$auth_user->getPermission('blog')) {
            return false;
        }

        if (empty($blog->owner)) {
            return false;
        }

        if ($auth_user->id == $blog->owner->id) // можно писать на своей стене
        {
            return true;
        }

        // если кто добавил кого то в черный список, то обмен сообщениями запрещен
        if ($auth_user->hasAddedToBlacklist($blog->owner)) {
            return false;
        }

        if ($auth_user->addedToBlacklistBy($blog->owner)) {
            return false;
        }

        if (empty($blog->create_user)) {
            return false;
        }

        // если кто добавил кого то в черный список, то обмен сообщениями запрещен
        if ($auth_user->hasAddedToBlacklist($blog->create_user)) {
            return false;
        }

        if ($auth_user->addedToBlacklistBy($blog->create_user)) {
            return false;
        }

        switch ($blog->owner->account_permissions->comment_on_the_wall) {
            case UserAccountPermissionValues::everyone:
                return true;
                break;
            case UserAccountPermissionValues::friends:
                if (!isset($auth_user)) {
                    return false;
                }
                return $auth_user->isFriendOf($blog->owner);
                break;
            case UserAccountPermissionValues::friends_and_subscribers:
                if (!isset($auth_user)) {
                    return false;
                }
                if ($auth_user->isFriendOf($blog->owner) or $auth_user->isSubscriberOf($blog->owner)) {
                    return true;
                }
                break;
            case UserAccountPermissionValues::friends_and_subscriptions:
                if (!isset($auth_user)) {
                    return false;
                }
                if ($auth_user->isFriendOf($blog->owner) or $auth_user->isSubscriptionOf($blog->owner)) {
                    return true;
                }
                break;
            case UserAccountPermissionValues::me:
                return false;
                break;
        }

        return false;
    }

    /**
     * Закрепление сообщения
     *
     * @param  User  $auth_user
     * @param  Blog  $blog
     */
    public function fix(User $auth_user, Blog $blog)
    {
        // сообщение должно быть на этой же стене, если это не так то запрещаем закрепление
        if (!$blog->isCreateOwner()) {
            return false;
        }

        // запрещаем если сообщение вложенное
        if (!$blog->isRoot()) {
            return false;
        }

        if ($blog->isFixed()) {
            return false;
        }

        if ($blog->isUserCreator($auth_user)) {
            if ($auth_user->getPermission('blog')) {
                return true;
            }
        } else {
            if ($auth_user->getPermission('blog_other_user')) {
                return true;
            }
        }

        return false;
    }

    /**
     * Открепление сообщения
     *
     * @param  User  $auth_user
     * @param  Blog  $blog
     */
    public function unfix(User $auth_user, Blog $blog)
    {
        if (!$blog->isFixed()) {
            return false;
        }

        if ($blog->isUserBlog($auth_user)) {
            if ($auth_user->getPermission('blog')) {
                return true;
            }
        } else {
            if ($auth_user->getPermission('blog_other_user')) {
                return true;
            }
        }

        return false;
    }

    /**
     * Редактирование сообщения
     *
     * @param  User  $auth_user
     * @param  Blog  $blog
     */
    public function update(User $auth_user, Blog $blog)
    {
        if (!$auth_user->getPermission('blog')) {
            return false;
        }

        if ($blog->isUserCreator($auth_user)) {
            return true;
        } else {
            if ($auth_user->getPermission('blog_other_user')) {
                return true;
            }
        }

        return false;
    }

    /**
     * Удаления сообщения
     *
     * @param  User  $auth_user
     * @param  Blog  $blog
     */
    public function delete(User $auth_user, Blog $blog)
    {
        // сообщение уже удалено
        if ($blog->trashed()) {
            return false;
        }

        if (!$auth_user->getPermission('blog')) {
            return false;
        }

        if ($blog->isUserBlog($auth_user)) {
            return true;
        }

        if ($blog->isUserCreator($auth_user)) {
            return true;
        }

        if (!$blog->isUserCreator($auth_user)) {
            if ($auth_user->getPermission('blog_other_user')) {
                return true;
            }
        }

        return false;
    }

    /**
     * Восстановление сообщения
     *
     * @param  User  $auth_user
     * @param  Blog  $blog
     */
    public function restore(User $auth_user, Blog $blog)
    {
        // сообщение не удалено
        if (!$blog->trashed()) {
            return false;
        }

        if (!$auth_user->getPermission('blog')) {
            return false;
        }

        if ($blog->isUserBlog($auth_user)) {
            return true;
        }

        if ($blog->isUserCreator($auth_user)) {
            return true;
        }

        if (!$blog->isUserCreator($auth_user)) {
            if ($auth_user->getPermission('blog_other_user')) {
                return true;
            }
        }

        return false;
    }

    /**
     * Можно ли просмотреть данные об устройстве
     *
     * @param  User  $auth_user
     */
    public function see_technical_information(User $auth_user, Blog $blog)
    {
        if (empty($blog->user_agent_id)) {
            return false;
        }

        return (boolean) $auth_user->getPermission('display_technical_information');
    }

    /**
     * Можно ли пользователю опубликовать пост
     *
     * @param  User  $auth_user
     * @param  Blog  $blog
     */
    public function approve(User $auth_user, Blog $blog)
    {
        if (!$blog->isSentForReview()) {
            return false;
        }

        return @(boolean) $auth_user->getPermission('check_post_comments');
    }

    /**
     * Просматривать посты на проверке
     *
     * @param  User  $auth_user
     */
    public function viewOnCheck(User $auth_user)
    {
        return @(boolean) $auth_user->getPermission('check_post_comments');
    }

    /**
     * Может ли пользователь пожаловаться на сообщение на стене
     *
     * @param  User  $auth_user
     * @param  Blog  $blog
     */
    public function complain(User $auth_user, Blog $blog)
    {
        if ($blog->trashed()) {
            return false;
        }

        return (boolean) $auth_user->getPermission('Complain');
    }
}
