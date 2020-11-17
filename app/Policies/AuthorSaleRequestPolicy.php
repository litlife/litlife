<?php

namespace App\Policies;

use App\AuthorSaleRequest;
use App\User;

class AuthorSaleRequestPolicy
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
     * Можно ли пользователю просмотреть заявку
     *
     * @param  User  $auth_user
     * @param  AuthorSaleRequest  $request
     */
    public function show(User $auth_user, AuthorSaleRequest $request)
    {
        if ($request->isUserCreator($auth_user)) {
            return true;
        }

        if ($request->isSentForReview()) {
            return false;
        }

        return (boolean) $auth_user->getPermission('author_sale_request_review');
    }

    /**
     * Можно ли пользователю одобрить заявку
     *
     * @param  User  $auth_user
     * @param  AuthorSaleRequest  $request
     */
    public function continue_review(User $auth_user, AuthorSaleRequest $request)
    {
        if (!$request->isReviewStarts()) {
            return false;
        }

        if ($auth_user->id != $request->status_changed_user_id) {
            return false;
        }

        return (boolean) $auth_user->getPermission('author_sale_request_review');
    }

    /**
     * Можно ли пользователю одобрить заявку
     *
     * @param  User  $auth_user
     * @param  AuthorSaleRequest  $request
     */
    public function accept(User $auth_user, AuthorSaleRequest $request)
    {
        if (!$request->isReviewStarts()) {
            return false;
        }

        return (boolean) $auth_user->getPermission('author_sale_request_review');
    }

    /**
     * Можно ли пользователю отклонить заявку
     *
     * @param  User  $auth_user
     * @param  AuthorSaleRequest  $request
     */
    public function reject(User $auth_user, AuthorSaleRequest $request)
    {
        if (!$request->isReviewStarts()) {
            return false;
        }

        return (boolean) $auth_user->getPermission('author_sale_request_review');
    }

    /**
     * Можно ли пользователю начать обрабатывать заявку
     *
     * @param  User  $auth_user
     * @param  AuthorSaleRequest  $request
     */
    public function start_review(User $auth_user, AuthorSaleRequest $request)
    {
        if ($request->isReviewStarts()) {
            return false;
        }

        if ($request->isAccepted()) {
            return false;
        }

        return (boolean) $auth_user->getPermission('author_sale_request_review');
    }

    /**
     * Можно ли пользователю отменить рассмотр заявки
     *
     * @param  User  $auth_user
     * @param  AuthorSaleRequest  $request
     */
    public function stop_review(User $auth_user, AuthorSaleRequest $request)
    {
        if (!$request->isReviewStarts()) {
            return false;
        }

        if ($auth_user->id != $request->status_changed_user_id) {
            return false;
        }

        return (boolean) $auth_user->getPermission('author_sale_request_review');
    }
}
