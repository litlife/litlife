<?php

namespace App\Policies;

use App\User;

class LikePolicy extends Policy
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
     * Можно ли пользователю ставить лайки
     *
     * @param  User  $auth_user
     */
    public function create(User $auth_user)
    {
        return (boolean) $auth_user->getPermission('LikeClick');
    }

}
