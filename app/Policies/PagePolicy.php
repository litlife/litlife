<?php

namespace App\Policies;

use App\Page;
use App\User;

class PagePolicy
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
     * Можно ли пользователю просмотреть страницу этой книги
     *
     * @param  User  $auth_user
     * @param  Page  $page
     */
    public function view(?User $auth_user, Page $page)
    {
        if ($page->book->isRejected())
        {
            if (!empty($auth_user)) {
                if ($page->book->purchases->where('buyer_user_id', $auth_user->id)->first()) {
                    return true;
                }
            }

            return false;
        }

        if (!$page->book->isReadAccess()) {
            if (!empty($auth_user) and optional($page->book->getManagerAssociatedWithUser($auth_user))->character == 'author') {
                return true;
            }

            if ($page->book->purchases->where('buyer_user_id', $auth_user->id)->first()) {
                return true;
            }

            if (@$auth_user->getPermission('access_to_closed_books')) {
                return true;
            } else {
                return false;
            }
        }

        if (!$page->section->isForSale()) {
            return true;
        } else {
            if ($page->page > $page->section->free_pages) {
                if (!empty($auth_user) and optional($page->book->getManagerAssociatedWithUser($auth_user))->character == 'author') {
                    return true;
                }

                if ($page->book->purchases->where('buyer_user_id', $auth_user->id)->first()) {
                    return true;
                } else {
                    return false;
                }
            } else {
                return true;
            }
        }


    }

    /**
     * Отображать ли рекламу на странице книги
     *
     * @param  User  $auth_user
     * @param  Page  $page
     */
    public function display_ads(?User $auth_user, Page $page)
    {
        if ($page->book->isForSale()) {
            return false;
        }

        if (!empty($auth_user)) {
            if (!$auth_user->can('display_ads', $page->book)) {
                return false;
            }
        }

        if ($page->character_count < config('litlife.minimum_number_of_characters_per_page_to_display_ads')) {
            return false;
        }

        return true;
    }
}
