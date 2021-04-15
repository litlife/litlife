<?php

namespace App\Policies;

use App\Author;
use App\User;

class AuthorPolicy extends Policy
{
    public function view(?User $auth_user, Author $author)
    {
        // книга в личном облаке
        if ($author->isPrivate()) {
            // разрешаем только если пользователь создатель
            if ($author->isUserCreator($auth_user)) {
                return true;
            }
        } else {
            return true;
        }
    }

    public function create(User $auth_user)
    {
        return true;
    }

    public function update(User $auth_user, Author $author)
    {
        // книга в личном облаке
        if ($author->isPrivate()) {
            // разрешаем только если пользователь создатель
            if ($author->isUserCreator($auth_user)) {
                return true;
            }
        } else {
            // иначе автор может быть на модерации или в общей библиотеке
            if ($auth_user->can('manage', $author)) {
                return true;
            }

            return (boolean) $auth_user->getPermission('author_edit');
        }
    }

    /**
     * Можно ли управлять автором
     *
     * @param  User  $auth_user
     * @param  Author  $author
     * @return bool
     */
    public function manage(User $auth_user, Author $author)
    {
        $managers = $author->managers
            ->where('user_id', $auth_user->id);

        foreach ($managers as $manager) {
            if ($manager->isAccepted()) {
                return true;
            }
        }

        return false;
    }

    /**
     * Можно ли объединять автора
     *
     * @param  User  $auth_user
     * @return bool
     */
    public function merge(User $auth_user)
    {
        if ($auth_user->getPermission('merge_authors')) {
            return true;
        }

        return false;
    }

    public function delete(User $auth_user, Author $author)
    {
        if ($author->trashed()) // автор уже удален
        {
            return false;
        }

        // книга в личном облаке
        if ($author->isPrivate()) {
            // разрешаем только если пользователь создатель
            if ($author->isUserCreator($auth_user)) {
                return true;
            }
        } else {

            $managers = $author->managers;

            if ($manager = $managers->where('can_sale', true)->first()) {
                if ($manager->isAccepted()) {
                    return false;
                }
            }

            return (boolean) $auth_user->getPermission('delete_hide_author');
        }
    }

    public function restore(User $auth_user, Author $author)
    {
        if (!$author->trashed()) // автор не удален
        {
            return false;
        }

        // книга в личном облаке
        if ($author->isPrivate()) {
            // разрешаем только если пользователь создатель
            if ($author->isUserCreator($auth_user)) {
                return true;
            }
        } else {
            return (boolean) $auth_user->getPermission('delete_hide_author');
        }
    }

    /**
     * Добавить фотографию к автору
     *
     * @param  User  $auth_user
     * @param  Author  $author
     * @return bool
     */
    public function create_photo(User $auth_user, Author $author)
    {
        // книга в личном облаке
        if ($author->isPrivate()) {
            // разрешаем только если пользователь создатель
            if ($author->isUserCreator($auth_user)) {
                return true;
            }
        } else {
            // иначе автор может быть на модерации или в общей библиотеке
            if ($auth_user->can('manage', $author)) {
                return true;
            }

            return (boolean) $auth_user->getPermission('author_edit');
        }
    }

    /**
     * Группировать автора
     *
     * @param  User  $auth_user
     * @param  Author  $author
     * @return bool
     */
    public function group(User $auth_user, Author $author)
    {
        return (boolean) $auth_user->getPermission('author_group_and_ungroup');
    }

    /**
     * Разгруппировывать автора
     *
     * @param  User  $auth_user
     * @param  Author  $author
     * @return bool
     */
    public function ungroup(User $auth_user, Author $author)
    {
        return (boolean) $auth_user->getPermission('author_group_and_ungroup');
    }

    public function watch_activity_logs(User $auth_user, Author $author)
    {
        return @(boolean) $auth_user->getPermission('WatchActivityLogs');
    }

    /**
     * Может ли пользователь видеть техническую информацию об авторе
     *
     * @param  User  $auth_user
     * @return bool
     */
    public function display_technical_information(User $auth_user)
    {
        return @(boolean) $auth_user->getPermission('display_technical_information');
    }

    /**
     * Можно ли обновить счетчики автора
     *
     * @param  User  $auth_user
     * @param  Author  $author
     * @return bool
     */
    public function refresh_counters(User $auth_user, Author $author)
    {
        foreach ($author->managers as $manager) {
            if (
                $manager->user_id == $auth_user->id
                and $manager->isAuthorCharacter()
                and $manager->isAccepted()
            ) {
                return true;
            }
        }

        return @(boolean) $auth_user->getPermission('refresh_counters');
    }

    /**
     * Можно ли опубликовать страницу автора
     *
     * @param  User  $auth_user
     * @param  Author  $author
     * @return bool
     */
    public function makeAccepted(User $auth_user, Author $author)
    {
        if ($author->isAccepted()) {
            return false;
        }

        return @(boolean) $auth_user->getPermission('check_books');
    }

    /**
     * Можно ли обновить счетчики автора
     *
     * @param  User  $auth_user
     * @param  Author  $author
     * @return bool
     */
    public function booksCloseAccess(User $auth_user, Author $author)
    {
        return (boolean) $auth_user->getPermission('book_secret_hide_set');
    }

    /**
     * Может ли пользователь просмотреть заявку на продажи книг
     *
     * @param  User  $auth_user
     * @param  Author  $author
     */
    /*
    public function view_sales_request(User $auth_user, Author $author)
    {
        if (!$auth_user->can('use_shop', User::class))
            return false;

        $manager = $author->managers
            ->where('character', 'author')
            ->where('user_id', $auth_user->id)
            ->first();

        if (empty($manager))
            return false;

        if (!$manager->isAccepted())
            return false;

        return true;
    }
    */

    /**
     * Может ли пользователь подать заявку на продажи книг
     *
     * @param  User  $auth_user
     * @param  Author  $author
     * @return bool|\Illuminate\Auth\Access\Response
     */
    public function sales_request(User $auth_user, Author $author)
    {
        if (!$auth_user->can('use_shop', User::class)) {
            return false;
        }

        $managers = $author->managers
            ->where('character', 'author');

        if ($managers->isEmpty()) {
            return false;
        }

        if ($manager = $managers->where('can_sale', true)->first()) {
            if ($manager->isAccepted()) {
                return false;
            }
        }

        if ($manager = $managers->where('user_id', $auth_user->id)->first()) {
            if (!$manager->isAccepted()) {
                return false;
            }

            if ($manager->isAccepted() and $manager->can_sale) {
                return false;
            }
        } else {
            return false;
        }

        $author_sale_request = $author->sales_request()
            ->where('create_user_id', $auth_user->id)
            ->whereStatusIn(['Rejected'])
            ->first();


        if (!empty($author_sale_request) and $author_sale_request->isRejected()) {
            if ($author_sale_request->created_at->addDays(config('litlife.minimum_days_to_submit_a_new_request_for_author_sale'))->isFuture()) {
                return $this->deny(__('author_sale_request.rejected').'. '.__('author_sale_request.you_can_submit_a_new_application_in_days',
                        ['days' => config('litlife.minimum_days_to_submit_a_new_request_for_author_sale')]));
            }
        }

        $author_sale_request = $author->sales_request()
            ->where('create_user_id', $auth_user->id)
            ->whereStatusIn(['OnReview', 'ReviewStarts'])
            ->first();

        if (!empty($author_sale_request)) {
            return false;
        }

        return true;
    }

    /**
     * Может ли пользователь подать заявку на верификацию
     *
     * @param  User  $auth_user
     * @param  Author  $author
     * @return bool|\Illuminate\Auth\Access\Response
     */
    public function verficationRequest(User $auth_user, Author $author)
    {
        if ($author->trashed()) {
            return false;
        }

        $managers = $author->managers;

        foreach ($managers as $manager) {
            if ($manager->isPrivate() and $manager->create_user->is($auth_user)) {
                return $this->deny(__('The verification request has already been sent'));
            }

            if ($manager->isAccepted()) {
                return $this->deny(__('The verification request has already been approved'));
            }

            if ($manager->user_id == $auth_user->id) {
                if ($manager->isSentForReview() or $manager->isReviewStarts()) {
                    return $this->deny(__('The verification request is waiting for review'));
                }
            }
        }

        return $auth_user->getPermission('author_editor_request');
    }

    /**
     * Может ли пользователь подать заявку "Хочу стать редактором"
     *
     * @param  User  $auth_user
     * @param  Author  $author
     * @return bool|\Illuminate\Auth\Access\Response
     */
    public function editorRequest(User $auth_user, Author $author)
    {
        if ($author->trashed()) {
            return false;
        }

        return $this->deny(__('Submission of new applications for editors authors is closed'));
        /*
                $managers = $author->managers;

                foreach ($managers as $manager) {
                    if ($manager->user_id == $auth_user->id) {
                        if ($manager->isSentForReview())
                            return false;

                        if ($manager->isPrivate())
                            return false;

                        if ($manager->isAccepted())
                            return false;
                    }
                }
                */

        return $auth_user->getPermission('author_editor_request');
    }

    /**
     * Может ли пользователь просмотреть список авторов и редакторов
     *
     * @param  User  $auth_user
     * @param  Author  $author
     * @return bool
     */
    public function viewManagers(User $auth_user, Author $author)
    {
        return (boolean) $auth_user->getPermission('moderator_add_remove');
    }

    /**
     * Может ли пользователь отключить для автора возможность продаж
     *
     * @param  User  $auth_user
     * @param  Author  $author
     * @return bool
     */
    public function salesDisable(User $auth_user, Author $author)
    {
        if (!$auth_user->getPermission('author_sale_request_review')) {
            return false;
        }

        $managers = $author->managers;

        if ($manager = $managers->where('can_sale', true)->first()) {
            if ($manager->isAccepted()) {
                return true;
            }
        }

        return false;
    }
}
