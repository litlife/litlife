<?php

namespace App\Policies;

use App\Image;
use App\User;

class ImagePolicy extends Policy
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
     * Можно ли пользователю добавить изображение
     *
     * @param  User  $auth_user
     */
    public function create(User $auth_user)
    {
        return true;
    }

    /**
     * Можно ли пользователю удалить изображение
     *
     * @param  User  $auth_user
     * @param  Image  $image
     */
    public function delete(User $auth_user, Image $image)
    {
        if ($image->isUserCreator($auth_user)) {
            return true;
        }
    }

}
