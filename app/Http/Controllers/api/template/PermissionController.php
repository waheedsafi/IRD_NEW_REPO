<?php

namespace App\Http\Controllers\api\template;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\SubPermission;
use App\Models\RolePermission;
use App\Models\UserPermission;
use App\Models\RolePermissionSub;
use App\Models\UserPermissionSub;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Repositories\Permission\PermissionRepositoryInterface;

class PermissionController extends Controller
{
    protected $permissionRepository;

    public function __construct(PermissionRepositoryInterface $permissionRepository)
    {
        $this->permissionRepository = $permissionRepository;
    }
    public function userPermissions($id)
    {
        $user = User::where('id', $id)->first();
        if (!$user) {
            return response()->json([
                'message' => __('app_translation.user_not_found'),
            ], 404, [], JSON_UNESCAPED_UNICODE);
        }
        return response()->json(
            $this->permissionRepository->assigningPermissions($user->id, $user->role_id),
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
            array_push(
                $userSubPermission,
                [
                    'id'     => $subPermission->id,
                    'name'   => $subPermission->name,
                    'edit'   => (bool) $subPermission->edit,
                    'delete' => (bool) $subPermission->delete,
                    'add'    => (bool) $subPermission->add,
                    'view'   => (bool) $subPermission->view,
                ]
            );
        }

        return response()->json(
            $userSubPermission,
            200,
            [],
            JSON_UNESCAPED_UNICODE
        );
    }

    public function singleUserEditPermission(Request $request)
    {
        $request->validated([
            "subPermissions" => "required",
            "permission" => "required",
            "user_id" => "required",
        ]);
        // Retrieve the subPermissions array from the request
        $subPermissions = $request->input('subPermissions');
        $permission = $request->input('permission');
        $user_id = $request->input('user_id');

        // Loop through the subPermissions array
        foreach ($subPermissions as $permission) {
            // Access each permission's properties
            $id = $permission['id'];
            $name = $permission['name'];
            $edit = $permission['edit'];
            $delete = $permission['delete'];
            $add = $permission['add'];
            $view = $permission['view'];

            // You can now use these variables to do something with the data
            // For example, updating permissions or storing them in a database

            // Example: Print the permission details
            Log::info("Permission ID: $id, Name: $name, Edit: $edit, Delete: $delete, Add: $add, View: $view");
        }

        return response()->json([
            'message' => __('app_translation.success'),
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }
    public function userPermissionUpdate(Request $request)
    {
        $request->validate([
            'user_id' => 'required|integer',
            'view' => 'nullable|boolean',  // Make 'view' optional if not provided
            'permission' => 'required|string',
            'subPermissions' => 'array'   // assuming subPermissions is an array of objects
        ]);

        // Retrieve the role of the user directly
        $role = User::find($request->user_id)->role_id;

        if (!$role) {
            return response()->json([
                'message' => __('app_translation.unauthorized'),
            ], 403, [], JSON_UNESCAPED_UNICODE);
        }

        // Check if the role permission exists
        $rolePermissionExists = RolePermission::where('role', $role)->where('permission', $request->permission)->exists();

        if (!$rolePermissionExists) {
            return response()->json([
                'message' => __('app_translation.unauthorized'),
            ], 403, [], JSON_UNESCAPED_UNICODE);
        }

        // Find or create the user permission
        $userPermission = UserPermission::firstOrCreate(
            ['user_id' => $request->user_id, 'permission' => $request->permission],
            ['view' => $request->view ?? false, 'visible' => $request->visible ?? false]
        );

        // If subPermissions exist, process them
        if (!empty($request->subPermissions)) {
            // Batch insert sub-permissions
            $subPermissionsToCreate = [];

            foreach ($request->subPermissions as $subPermission) {
                // Fetch sub-permission values in a single query
                $subPermissionValue = RolePermissionSub::select('edit', 'delete', 'view', 'add')
                    ->where('role_permission_id', $role)
                    ->where('sub_permission_id', $subPermission['id'])
                    ->first(); // Use first() for a single result

                if ($subPermissionValue) {
                    // Check if the user already has this sub-permission, update if exists, else add new
                    $existingSubPermission = UserPermissionSub::where('user_permission_id', $userPermission->id)
                        ->where('sub_permission_id', $subPermission['id'])
                        ->first();

                    if ($existingSubPermission) {
                        // Update the existing sub-permission
                        $existingSubPermission->update([
                            'edit' => $subPermissionValue->edit ?? 0,
                            'delete' => $subPermissionValue->delete ?? 0,
                            'add' => $subPermissionValue->add ?? 0,
                            'view' => $subPermissionValue->view ?? 0,
                        ]);
                    } else {
                        // Add new sub-permission
                        $subPermissionsToCreate[] = [
                            'user_permission_id' => $userPermission->id,
                            'sub_permission_id' => $subPermission['id'],
                            'edit' => $subPermissionValue->edit ?? 0,
                            'delete' => $subPermissionValue->delete ?? 0,
                            'add' => $subPermissionValue->add ?? 0,
                            'view' => $subPermissionValue->view ?? 0,
                        ];
                    }
                }
            }

            // Insert sub-permissions in batch if needed
            if (!empty($subPermissionsToCreate)) {
                UserPermissionSub::insert($subPermissionsToCreate);
            }
        }

        return response()->json([
            'message' => __('app_translation.success'),
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }
    public function editUserPermissions(Request $request)
    {
        $request->validated([
            'user_id' => "required",
            'permissions' => "required"
        ]);
        $permissions = $request->permissions;
        $user_id = $request->user_id;
        $user = User::where('id', $user_id)->first();
        if (!$user) {
            return response()->json([
                'message' => __('app_translation.user_not_found'),
            ], 404, [], JSON_UNESCAPED_UNICODE);
        }
        $rolePermissions = $this->permissionRepository->rolePermissions($user->role_id);
        $formattedRolePermissions = $this->permissionRepository->formatRolePermissions($rolePermissions);

        foreach ($permissions as $permission) {
            $perm = $formattedRolePermissions->where("permission", $permission->permission)->first();
            // 1. If permission not found set
            if (!$perm) {
                return response()->json([
                    'message' => __('app_translation.unauthorized_role_per'),
                ], 403, [], JSON_UNESCAPED_UNICODE);
            } else {
                // 2. If permission found check for any missing Sub Permissions
                $permSub = $perm->sub;
                foreach ($permission->sub as $subPermission) {
                    $subExists = true;
                    for ($i = 0; $i < count($permSub); $i++) {
                        $sub = $permSub[$i];
                        if ($sub['id'] != $subPermission['id']) {
                            $subExists = false;
                            break;
                        }
                    }
                    if (!$subExists) {
                        return response()->json([
                            'message' => __('app_translation.unauthorized_role_sub_per'),
                        ], 403, [], JSON_UNESCAPED_UNICODE);
                    } else {
                        // Permission and sub permission exist update
                    }
                }
            }
        }
    }
}
