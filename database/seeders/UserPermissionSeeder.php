<?php

namespace Database\Seeders;

use App\Enums\RoleEnum;
use App\Models\UserPermission;
use Illuminate\Database\Seeder;
use App\Enums\SubPermissionEnum;
use App\Models\UserPermissionSub;

class UserPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->superPermissions();
        $this->adminPermissions();
        $this->userPermissions();
        $this->debuggerPermissions();
    }

    public function superPermissions()
    {
        UserPermission::factory()->create([
            "view" => true,
            "edit" => true,
            "delete" => true,
            "add" => true,
            "user_id" => RoleEnum::super->value,
            "permission" => "dashboard"
        ]);
        $userPermission = UserPermission::factory()->create([
            "view" => true,
            "edit" => true,
            "delete" => true,
            "add" => true,
            "user_id" => RoleEnum::super->value,
            "permission" => "ngo"
        ]);
        $this->addNgoSubPermissions($userPermission);
        UserPermission::factory()->create([
            "view" => true,
            "edit" => true,
            "delete" => true,
            "add" => true,
            "user_id" => RoleEnum::super->value,
            "permission" => "donor"
        ]);
        UserPermission::factory()->create([
            "view" => true,
            "edit" => true,
            "delete" => true,
            "add" => true,
            "user_id" => RoleEnum::super->value,
            "permission" => "projects"
        ]);
        $userPermission = UserPermission::factory()->create([
            "view" => true,
            "edit" => true,
            "delete" => true,
            "add" => true,
            "user_id" => RoleEnum::super->value,
            "permission" => "management/news"
        ]);
        $userPermission = UserPermission::factory()->create([
            "view" => true,
            "edit" => true,
            "delete" => true,
            "add" => true,
            "user_id" => RoleEnum::super->value,
            "permission" => "management/about"
        ]);
        $this->addAboutSubPermissions($userPermission);
        $userPermission = UserPermission::factory()->create([
            "view" => true,
            "edit" => true,
            "delete" => true,
            "add" => true,
            "user_id" => RoleEnum::super->value,
            "permission" => "users"
        ]);
        $this->addUserSubPermissions($userPermission);
        $userPermission = UserPermission::factory()->create([
            "view" => true,
            "edit" => true,
            "delete" => true,
            "add" => true,
            "user_id" => RoleEnum::super->value,
            "permission" => "settings"
        ]);
        $this->addSettingSubPermissions($userPermission);
        UserPermission::factory()->create([
            "view" => true,
            "edit" => true,
            "delete" => true,
            "add" => true,
            "user_id" => RoleEnum::super->value,
            "permission" => "reports"
        ]);
        UserPermission::factory()->create([
            "view" => true,
            "edit" => true,
            "delete" => true,
            "add" => true,
            "user_id" => RoleEnum::super->value,
            "permission" => "logs"
        ]);
        UserPermission::factory()->create([
            "view" => true,
            "edit" => true,
            "delete" => true,
            "add" => true,
            "user_id" => RoleEnum::super->value,
            "permission" => "audit"
        ]);
    }
    public function adminPermissions()
    {
        UserPermission::factory()->create([
            "view" => true,
            "edit" => true,
            "delete" => true,
            "add" => true,
            "user_id" => RoleEnum::admin->value,
            "permission" => "dashboard"
        ]);
        $userPermission = UserPermission::factory()->create([
            "view" => true,
            "edit" => true,
            "delete" => true,
            "add" => true,
            "user_id" => RoleEnum::admin->value,
            "permission" => "ngo"
        ]);
        $this->addNgoSubPermissions($userPermission);

        UserPermission::factory()->create([
            "view" => true,
            "edit" => true,
            "delete" => true,
            "add" => true,
            "user_id" => RoleEnum::admin->value,
            "permission" => "donor"
        ]);
        UserPermission::factory()->create([
            "view" => true,
            "edit" => true,
            "delete" => true,
            "add" => true,
            "user_id" => RoleEnum::admin->value,
            "permission" => "projects"
        ]);
        $userPermission = UserPermission::factory()->create([
            "view" => true,
            "edit" => true,
            "delete" => true,
            "add" => true,
            "user_id" => RoleEnum::admin->value,
            "permission" => "management/news"
        ]);
        $userPermission = UserPermission::factory()->create([
            "view" => true,
            "edit" => true,
            "delete" => true,
            "add" => true,
            "user_id" => RoleEnum::admin->value,
            "permission" => "management/about"
        ]);
        $this->addAboutSubPermissions($userPermission);

        $userPermission = UserPermission::factory()->create([
            "view" => true,
            "edit" => true,
            "delete" => true,
            "add" => true,
            "user_id" => RoleEnum::admin->value,
            "permission" => "users"
        ]);
        $this->addUserSubPermissions($userPermission);

        $userPermission = UserPermission::factory()->create([
            "view" => true,
            "edit" => true,
            "delete" => true,
            "add" => true,
            "user_id" => RoleEnum::admin->value,
            "permission" => "settings"
        ]);
        $this->addSettingSubPermissions($userPermission);
        UserPermission::factory()->create([
            "view" => true,
            "edit" => true,
            "delete" => true,
            "add" => true,
            "user_id" => RoleEnum::admin->value,
            "permission" => "reports"
        ]);
    }
    public function userPermissions()
    {
        UserPermission::factory()->create([
            "view" => true,
            "edit" => true,
            "delete" => true,
            "add" => true,
            "user_id" => RoleEnum::user->value,
            "permission" => "dashboard"
        ]);
        $userPermission  = UserPermission::factory()->create([
            "view" => true,
            "edit" => true,
            "delete" => true,
            "add" => true,
            "user_id" => RoleEnum::user->value,
            "permission" => "ngo"
        ]);
        $this->addNgoSubPermissions($userPermission);
        UserPermission::factory()->create([
            "view" => true,
            "edit" => true,
            "delete" => true,
            "add" => true,
            "user_id" => RoleEnum::user->value,
            "permission" => "projects"
        ]);
        $userPermission = UserPermission::factory()->create([
            "view" => true,
            "edit" => true,
            "delete" => true,
            "add" => true,
            "user_id" => RoleEnum::user->value,
            "permission" => "management/news"
        ]);
        $userPermission = UserPermission::factory()->create([
            "view" => true,
            "edit" => true,
            "delete" => true,
            "add" => true,
            "user_id" => RoleEnum::user->value,
            "permission" => "management/about"
        ]);
        $this->addAboutSubPermissions($userPermission);
        UserPermission::factory()->create([
            "view" => true,
            "edit" => true,
            "delete" => true,
            "add" => true,
            "user_id" => RoleEnum::user->value,
            "permission" => "reports"
        ]);
        $userPermission = UserPermission::factory()->create([
            "view" => true,
            "edit" => true,
            "delete" => true,
            "add" => true,
            "user_id" => RoleEnum::user->value,
            "permission" => "settings"
        ]);
        $this->addSettingSubPermissions($userPermission);
    }
    public function debuggerPermissions()
    {
        UserPermission::factory()->create([
            "view" => true,
            "edit" => true,
            "delete" => true,
            "add" => true,
            "user_id" => RoleEnum::debugger->value,
            "permission" => "dashboard"
        ]);
        UserPermission::factory()->create([
            "view" => true,
            "edit" => true,
            "delete" => true,
            "add" => true,
            "user_id" => RoleEnum::debugger->value,
            "permission" => "logs"
        ]);
        $userPermission = UserPermission::factory()->create([
            "view" => true,
            "edit" => true,
            "delete" => true,
            "add" => true,
            "user_id" => RoleEnum::debugger->value,
            "permission" => "settings"
        ]);
        $this->addSettingSubPermissions($userPermission);
    }

    public function addUserSubPermissions($userPermission)
    {
        foreach (SubPermissionEnum::USERS as $id => $role) {
            UserPermissionSub::factory()->create([
                "edit" => true,
                "delete" => true,
                "add" => true,
                "view" => true,
                "user_permission_id" => $userPermission->id,
                "sub_permission_id" => $id,
            ]);
        }
    }
    public function addNgoSubPermissions($userPermission)
    {
        foreach (SubPermissionEnum::NGO as $id => $role) {
            UserPermissionSub::factory()->create([
                "edit" => true,
                "delete" => true,
                "add" => true,
                "view" => true,
                "user_permission_id" => $userPermission->id,
                "sub_permission_id" => $id,
            ]);
        }
    }
    public function addSettingSubPermissions($userPermission)
    {
        foreach (SubPermissionEnum::SETTINGS as $id => $role) {
            UserPermissionSub::factory()->create([
                "edit" => true,
                "delete" => true,
                "add" => true,
                "view" => true,
                "user_permission_id" => $userPermission->id,
                "sub_permission_id" => $id,
            ]);
        }
    }
    public function addAboutSubPermissions($userPermission)
    {
        foreach (SubPermissionEnum::ABOUT as $id => $role) {
            UserPermissionSub::factory()->create([
                "edit" => true,
                "delete" => true,
                "add" => true,
                "view" => true,
                "user_permission_id" => $userPermission->id,
                "sub_permission_id" => $id,
            ]);
        }
    }
}
