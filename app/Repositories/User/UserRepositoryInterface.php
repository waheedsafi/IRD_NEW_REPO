<?php

namespace App\Repositories\User;

interface UserRepositoryInterface
{
    /**
     * Retuns all user permissions.
     * 
     * @param string $user_id
     * @return mix
     */
    public function authFormattedPermissions($user_id);
}
