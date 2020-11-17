<?php

namespace App\Policies;

use App\Award;
use App\User;

class AwardPolicy extends Policy
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
        if (@$auth_user->getPermission('awards')) {
            return true;
        }
    }

    public function update(User $auth_user)
    {
        if (@$auth_user->getPermission('awards')) {
            return true;
        }
    }

    public function delete(User $auth_user, Award $award)
    {
        if ($award->trashed()) {
            return false;
        }

        if (@$auth_user->getPermission('awards')) {
            return true;
        }
    }

    public function restore(User $auth_user, Award $award)
    {
        if (!$award->trashed()) {
            return false;
        }

        if (@$auth_user->getPermission('awards')) {
            return true;
        }
    }

    public function attach(User $auth_user)
    {
        if (@$auth_user->getPermission('awards')) {
            return true;
        }
    }

    public function detach(User $auth_user)
    {
        if (@$auth_user->getPermission('awards')) {
            return true;
        }
    }
}
