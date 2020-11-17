<?php

namespace App\Policies;

use App\ForumGroup;
use App\User;

class ForumGroupPolicy extends Policy
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

    public function create(User $auth_user)
    {
        return (boolean) $auth_user->getPermission('forum_group_handle');
    }

    public function update(User $auth_user, ForumGroup $forum_group)
    {
        return (boolean) $auth_user->getPermission('forum_group_handle');
    }

    public function delete(User $auth_user, ForumGroup $forum_group)
    {
        return (boolean) $auth_user->getPermission('forum_group_handle');
    }

    public function restore(User $auth_user, ForumGroup $forum_group)
    {
        return (boolean) $auth_user->getPermission('forum_group_handle');
    }

    /**
     * Можно ли пользователю изменить порядок групп форумов
     *
     * @param  User  $auth_user
     */
    public function change_order(User $auth_user)
    {
        return (boolean) $auth_user->getPermission('forum_group_handle');
    }
}
