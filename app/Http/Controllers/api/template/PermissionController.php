<?php

namespace App\Http\Controllers\api\template;

use App\Http\Controllers\Controller;
use App\Models\RolePermission;
use App\Models\SubPermission;
use App\Models\UserPermission;
use App\Models\UserPermissionSub;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    public function permissions($id, Request $request)
    {
        $userPermissions = [];
        $user = $request->user();
        if ($user->grant_permission) {
            $userPermissions = RolePermission::where("role", '=', $id)->select("permission as name")->get();
        } else {
            return response()->json([
                'message' => __('app_translation.unauthorized'),
            ], 403, [], JSON_UNESCAPED_UNICODE);
        }

        return response()->json(
            $userPermissions,
            200,
            [],
            JSON_UNESCAPED_UNICODE
        );
    }

    public function subPermissions(Request $request)
    {
        $request->validate([
            'user_id' => 'required|integer',
            'permission' => 'required|string'
        ]);

        // Fetch user permission ID in a single query
        $userPermission = UserPermission::where('user_id', $request->user_id)
            ->where('permission', $request->permission)
            ->first(['id']);

        if (!$userPermission) {
            return response()->json([
                'message' => __('app_translation.not_found'),
            ], 404, [], JSON_UNESCAPED_UNICODE);
        }

        // Fetch all sub-permissions and user-specific permissions in a single query
        $subPermissions = SubPermission::leftJoin('user_permission_subs', function ($join) use ($userPermission) {
            $join->on('sub_permissions.id', '=', 'user_permission_subs.sub_permission_id')
                ->where('user_permission_subs.user_permission_id', '=', $userPermission->id);
        })
            ->where('sub_permissions.permission', $request->permission)
            ->get([
                'sub_permissions.id',
                'sub_permissions.name',
                'user_permission_subs.edit',
                'user_permission_subs.delete',
                'user_permission_subs.add',
                'user_permission_subs.view'
            ]);

        // Format response
        $userSubPermission = [];
        foreach ($subPermissions as $subPermission) {
            $userSubPermission[$subPermission->name] = [
                'id'     => $subPermission->id,
                'edit'   => (bool) $subPermission->edit,
                'delete' => (bool) $subPermission->delete,
                'add'    => (bool) $subPermission->add,
                'view'   => (bool) $subPermission->view,
            ];
        }

        return response()->json(
            $userSubPermission,
            200,
            [],
            JSON_UNESCAPED_UNICODE
        );
    }
}
