<?php

namespace App\Policies;

use App\User;
use App\UserPurchase;

class UserPurchasePolicy extends Policy
{
    /**
     * Отменить покупку
     *
     * @param  User  $auth_user
     * @param  UserPurchase  $purchase
     */
    public function cancel(User $auth_user, UserPurchase $purchase)
    {
        if ($purchase->isCanceled()) {
            return false;
        }

        return (boolean) $auth_user->getPermission('view_financial_statistics');
    }
}
