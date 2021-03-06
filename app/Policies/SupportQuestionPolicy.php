<?php

namespace App\Policies;

use App\SupportQuestion;
use App\User;

class SupportQuestionPolicy extends Policy
{
    /**
     * Может ли пользователь просмотреть список всех запросов в поддержку
     *
     * @param  User  $auth_user
     * @param  SupportQuestion  $request
     * @return mixed
     */
    public function view_index(User $auth_user)
    {
        return $auth_user->getPermission('reply_to_support_service');
    }

    /**
     * Может ли пользователь создать сообщение в запросе
     *
     * @param  User  $auth_user
     * @param  SupportQuestion  $request
     * @return mixed
     */
    public function createMessage(User $auth_user, SupportQuestion $request)
    {
        if ($request->isAccepted()) {
            return $this->deny(__('Question has already been resolved'));
        }

        if ($request->isUserCreator($auth_user)) {
            return true;
        } else {
            if ($request->isReviewStarts()) {
                if ($request->status_changed_user->is($auth_user)) {
                    if ($auth_user->getPermission('reply_to_support_service')) {
                        return $this->allow();
                    }
                }
            }
        }

        return $this->deny();
    }

    /**
     * Может ли пользователь просмотреть запрос в поддержку
     *
     * @param  User  $auth_user
     * @param  SupportQuestion  $request
     * @return mixed
     */
    public function show(User $auth_user, SupportQuestion $request)
    {
        if ($auth_user->getPermission('reply_to_support_service')) {
            return $this->allow();
        }

        if ($request->isUserCreator($auth_user)) {
            return $this->allow();
        }

        return $this->deny();
    }

    /**
     * Может ли пользователь отправить вопрос в поддержку
     *
     * @param  User  $auth_user
     * @param  SupportQuestion  $request
     * @return mixed
     */
    public function create(User $auth_user)
    {
        return $auth_user->getPermission('send_a_support_question');
    }

    /**
     * Can the user mark the support request as resolved
     *
     * @param  User  $auth_user
     * @param  SupportQuestion  $request
     * @return mixed
     */
    public function solve(User $auth_user, SupportQuestion $request)
    {
        if ($request->isAccepted()) {
            return $this->deny(__('The request is already marked as resolved'));
        }

        if ($request->isUserCreator($auth_user)) {
            if (!$request->isLatestMessageByCreatedUser()) {
                return $this->allow();
            }
        }

        if (!$request->isReviewStarts()) {
            return $this->deny(__('To mark a request as resolved you must first start reviewing it'));
        }

        if ($request->status_changed_user->id != $auth_user->id) {
            return $this->deny(__('You can\'t mark the request as resolved because you are not the one who is considering this request'));
        }

        return $auth_user->getPermission('reply_to_support_service');
    }


    /**
     * Можно ли пользователю начать рассматривать запрос
     *
     * @param  User  $auth_user
     * @param  SupportQuestion  $supportQuestion
     * @return mixed
     */
    public function startReview(User $auth_user, SupportQuestion $supportQuestion)
    {
        if (!$supportQuestion->isSentForReview()) {
            return $this->deny();
        }

        return (boolean) $auth_user->getPermission('reply_to_support_service');
    }

    /**
     * Можно ли пользователю прекратить рассматривать запрос
     *
     * @param  User  $auth_user
     * @param  SupportQuestion  $supportQuestion
     * @return mixed
     */
    public function stopReview(User $auth_user, SupportQuestion $supportQuestion)
    {
        if (!$supportQuestion->isReviewStarts()) {
            return $this->deny();
        }

        if ($supportQuestion->status_changed_user->id != $auth_user->id) {
            return $this->deny();
        }

        return (boolean) $auth_user->getPermission('reply_to_support_service');
    }

    /**
     * Может ли пользователь продолжить рассмотрение
     *
     * @param  User  $auth_user
     * @param  SupportQuestion  $supportQuestion
     * @return mixed
     */
    public function continueReviewing(User $auth_user, SupportQuestion $supportQuestion)
    {
        if (!$supportQuestion->isReviewStarts()) {
            return $this->deny();
        }

        if ($supportQuestion->status_changed_user->id != $auth_user->id) {
            return $this->deny();
        }

        return (boolean) $auth_user->getPermission('reply_to_support_service');
    }

    /**
     * Может ли пользователь продолжить рассмотрение
     *
     * @param  User  $auth_user
     * @param  SupportQuestion  $supportQuestion
     * @return mixed
     */
    public function create_feedback(User $auth_user, SupportQuestion $supportQuestion)
    {
        if (!$supportQuestion->isUserCreator($auth_user)) {
            return $this->deny(__('The question was created by another user'));
        }

        if (!$supportQuestion->isAccepted()) {
            return $this->deny(__('To write feedback the question must be reviewed'));
        }

        return $this->allow();
    }

    /**
     * Может ли пользователь отредактировать вопрос
     *
     * @param  User  $auth_user
     * @param  SupportQuestion  $supportQuestion
     * @return mixed
     */
    public function update(User $auth_user, SupportQuestion $supportQuestion)
    {
        if ($supportQuestion->isUserCreator($auth_user)) {
            return $this->allow();
        }

        if ($auth_user->getPermission('reply_to_support_service')) {
            return $this->allow();
        }

        return $this->deny(__('You don\'t have permission to edit the question'));
    }
}
