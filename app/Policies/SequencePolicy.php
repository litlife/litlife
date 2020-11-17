<?php

namespace App\Policies;

use App\Sequence;
use App\User;

class SequencePolicy extends Policy
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
     * Можно ли пользователю создать серию
     *
     * @param  User  $auth_user
     */
    public function create(User $auth_user)
    {
        return true;
    }

    /**
     * Можно ли пользователю отредактировать серию
     *
     * @param  User  $auth_user
     * @param  Sequence  $sequence
     */
    public function update(User $auth_user, Sequence $sequence)
    {
        if ($sequence->isPrivate()) {
            if ($sequence->isUserCreator($auth_user)) {
                return true;
            }
        } else {
            return (boolean) $auth_user->getPermission('SequenceEdit');
        }
    }

    /**
     * Можно ли пользователю удалить серию
     *
     * @param  User  $auth_user
     * @param  Sequence  $sequence
     */
    public function delete(User $auth_user, Sequence $sequence)
    {
        if ($sequence->trashed()) // серия уже удалена, поэтому запрещаем
        {
            return false;
        }

        if ($sequence->isPrivate()) {
            if ($sequence->isUserCreator($auth_user)) {
                return true;
            }
        } else {
            return (boolean) $auth_user->getPermission('SequenceDelete');
        }
    }

    /**
     * Можно ли пользователю восстановить серию
     *
     * @param  User  $auth_user
     * @param  Sequence  $sequence
     */
    public function restore(User $auth_user, Sequence $sequence)
    {
        if (!$sequence->trashed()) // серия не была удалена, поэтому запрещаем
        {
            return false;
        }

        if ($sequence->isPrivate()) {
            if ($sequence->isUserCreator($auth_user)) {
                return true;
            }
        } else {
            return (boolean) $auth_user->getPermission('SequenceDelete');
        }
    }

    /**
     * Можно ли пользователю отредактировать номера книг в серии
     *
     * @param  User  $auth_user
     * @param  Sequence  $sequence
     */
    public function book_numbers_edit(User $auth_user, Sequence $sequence)
    {
        if ($sequence->isPrivate()) {
            if ($sequence->isUserCreator($auth_user)) {
                return true;
            }
        } else {
            return (boolean) $auth_user->getPermission('SequenceEdit');
        }
    }

    /**
     * Можно ли пользователю присоединить серию с другой
     *
     * @param  User  $auth_user
     * @param  Sequence  $sequence
     */
    public function merge(User $auth_user, Sequence $sequence)
    {
        if ($sequence->isMerged()) {
            return false;
        }

        if ($sequence->isPrivate()) {

        } else {
            return (boolean) $auth_user->getPermission('SequenceMerge');
        }
    }

    /**
     * Можно ли пользователю отсоединять серию
     *
     * @param  User  $auth_user
     * @param  Sequence  $sequence
     */
    public function unmerge(User $auth_user, Sequence $sequence)
    {
        if (!$sequence->isMerged()) {
            return false;
        }

        if ($sequence->isPrivate()) {
            if ($sequence->isUserCreator($auth_user)) {
                return true;
            }
        } else {
            return (boolean) $auth_user->getPermission('SequenceMerge');
        }
    }

    public function watch_activity_logs(User $auth_user, Sequence $sequence)
    {
        return @(boolean) $auth_user->getPermission('WatchActivityLogs');
    }
}
