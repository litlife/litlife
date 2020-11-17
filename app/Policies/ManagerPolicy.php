<?php

namespace App\Policies;

use App\Manager;
use App\User;

class ManagerPolicy extends Policy
{
    /**
     * Можно ли пользователю просматривать редакторов или авторов которые находятся на модерации
     *
     * @param  User  $auth_user
     */
    public function viewOnCheck(User $auth_user)
    {
        return (boolean) $auth_user->getPermission('author_editor_check');
    }

    /**
     * Можно ли пользователю просмототреть заявку
     *
     * @param  User  $auth_user
     * @param  Manager  $manager
     */
    public function view(User $auth_user, Manager $manager)
    {
        if ($auth_user->id == $manager->user_id) {
            return true;
        }

        return (boolean) $auth_user->getPermission('moderator_add_remove');
    }

    /**
     * Можно ли пользователю добавлять модераторов к странице автора
     *
     * @param  User  $auth_user
     */
    public function create(User $auth_user)
    {
        return (boolean) $auth_user->getPermission('moderator_add_remove');
    }

    /**
     * Можно ли пользователю убирать модераторов от страницы автора
     *
     * @param  User  $auth_user
     * @param  Manager  $manager
     */
    public function delete(User $auth_user, Manager $manager)
    {
        if ($manager->trashed()) {
            return false;
        }

        if ($manager->can_sale) {
            return false;
        }

        if ($auth_user->id == $manager->user_id) {
            return true;
        }

        return (boolean) $auth_user->getPermission('moderator_add_remove');
    }

    /**
     * Можно ли пользователю восстанавливать редакторов
     *
     * @param  User  $auth_user
     * @param  Manager  $manager
     */
    public function restore(User $auth_user, Manager $manager)
    {
        return false;
        /*
        if (!$manager->trashed())
            return false;

        if ($auth_user->id == $manager->user_id)
        {
            if (!$manager->isRejected())
                return true;
        }

        return (boolean)$auth_user->getPermission('ModeratorAddRemove');
        */
    }

    /**
     * Можно ли пользователю отправлять и редактировать заявки стать редактором или автором
     *
     * @param  User  $auth_user
     */
    public function request(User $auth_user)
    {
        return (boolean) $auth_user->getPermission('author_editor_request');
    }

    /**
     * Можно ли пользователю проверять заявки
     *
     * @param  User  $auth_user
     * @param  Manager  $manager
     */
    public function approve(User $auth_user, Manager $manager)
    {
        if (empty($manager->manageable) or $manager->manageable->trashed()) {
            return false;
        }

        if (!$manager->isReviewStarts()) {
            return false;
        }

        if ($manager->status_changed_user_id != $auth_user->id) {
            return false;
        }

        if ($manager->isRejected()) {
            return false;
        }

        if (!$manager->manageable->isAccepted()) {
            return false;
        }

        return (boolean) $auth_user->getPermission('author_editor_check');
    }

    /**
     * Можно ли пользователю отклонять заявки
     *
     * @param  User  $auth_user
     * @param  Manager  $manager
     */
    public function decline(User $auth_user, Manager $manager)
    {
        if (!$manager->isReviewStarts()) {
            return false;
        }

        if ($manager->status_changed_user_id != $auth_user->id) {
            return false;
        }

        if ($manager->isAccepted()) {
            return false;
        }

        if (!empty($manager->manageable)) {
            if (!$manager->manageable->isAccepted()) {
                return false;
            }
        }

        return (boolean) $auth_user->getPermission('author_editor_check');
    }

    /**
     * Можно ли пользователю начать рассматривать заявку
     *
     * @param  User  $auth_user
     * @param  Manager  $manager
     */
    public function startReview(User $auth_user, Manager $manager)
    {
        if ($manager->isReviewStarts()) {
            return false;
        }

        if ($manager->isRejected()) {
            return false;
        }

        if ($manager->isAccepted()) {
            return false;
        }

        if (!empty($manager->manageable)) {
            if (!$manager->manageable->isAccepted()) {
                return false;
            }
        }

        return (boolean) $auth_user->getPermission('author_editor_check');
    }

    /**
     * Можно ли пользователю отменить заявку
     *
     * @param  User  $auth_user
     * @param  Manager  $manager
     */
    public function stopReview(User $auth_user, Manager $manager)
    {
        if (!$manager->isReviewStarts()) {
            return false;
        }

        if ($manager->status_changed_user_id != $auth_user->id) {
            return false;
        }

        if ($manager->isRejected()) {
            return false;
        }

        if ($manager->isAccepted()) {
            return false;
        }

        return (boolean) $auth_user->getPermission('author_editor_check');
    }
}
