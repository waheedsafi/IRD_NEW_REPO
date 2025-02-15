<?php

namespace App\Repositories\User;

use Illuminate\Support\Facades\DB;

class UserRepository implements UserRepositoryInterface
{
    public function formattedPermissions($user_id)
    {
        $permissions = DB::table('users as u')
            ->where('u.id', $user_id)
            ->join('user_permissions as up', 'u.id', '=', 'up.user_id')
            ->join('permissions as p', 'up.permission', '=', 'p.name')
            ->leftJoin('user_permission_subs as ups', 'up.id', '=', 'ups.user_permission_id')
            // ->leftJoin('sub_permissions as sp', 'ups.sub_permission_id', '=', 'sp.id')
            ->select(
                'up.id as user_permission_id',
                'p.name as permission',
                'p.icon',
                'p.priority',
                'up.view',
                'up.visible',
                DB::raw('ups.sub_permission_id as sub_permission_id'),
                DB::raw('ups.add as sub_add'),
                DB::raw('ups.delete as sub_delete'),
                DB::raw('ups.edit as sub_edit'),
                DB::raw('ups.view as sub_view')
            )
            ->orderBy('p.priority')  // Optional: If you want to order by priority, else remove
            ->get();

        // Transform data to match desired structure (for example, if you need nested `sub` permissions)
        $formattedPermissions = $permissions->groupBy('user_permission_id')->map(function ($group) {
            $subPermissions = $group->filter(function ($item) {
                return $item->sub_permission_id !== null; // Filter for permissions that have sub-permissions
            });

            $permission = $group->first(); // Get the first permission for this group

            $permission->view = $permission->view == 1;  // Convert 1 to true, 0 to false
            $permission->visible = $permission->visible == 1;  // Convert 1 to true, 0 to false
            if ($subPermissions->isNotEmpty()) {

                $permission->sub = $subPermissions->map(function ($sub) {
                    return [
                        'id' => $sub->sub_permission_id,
                        'add' => $sub->sub_add == 1,   // Convert 1 to true, 0 to false
                        'delete' => $sub->sub_delete == 1,  // Convert 1 to true, 0 to false
                        'edit' => $sub->sub_edit == 1,   // Convert 1 to true, 0 to false
                        'view' => $sub->sub_view == 1,   // Convert 1 to true, 0 to false
                    ];
                });
            } else {
                $permission->sub = [];
            }
            // If there are no sub-permissions, remove the unwanted fields
            unset($permission->sub_permission_id);
            unset($permission->sub_add);
            unset($permission->sub_delete);
            unset($permission->sub_edit);

            return $permission;
        })->values();

        return $formattedPermissions;
    }
}
