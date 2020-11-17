<?php

namespace App\Policies;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\Access\HandlesAuthorization;

class Policy
{
    use HandlesAuthorization;
    /*
        protected function deny($message = '')
        {
            throw new AuthorizationException(empty($message) ? __('user.unauthorized_error') : $message);
        }
        */
}
