<?php

namespace App\Repositories\Permission;

interface PermissionRepositoryInterface
{

    /**
     * Retrieve NGO data when registeration is completed.
     * 
     *
     * @param string $user_id
     * @param string $role_id
     * @return @var \Illuminate\Support\Collection<int, \stdClass|null> $formattedPermissions
     */
    public function assigningPermissions($user_id, $role_id);
}
