<?php

namespace Database\Seeders;

use App\Enums\RoleEnum;
use App\Models\RolePermission;
use Illuminate\Database\Seeder;
use App\Enums\SubPermissionEnum;
use App\Models\RolePermissionSub;

class RolePermissionSeeder extends Seeder
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
        $this->ngoPermissions();
        $this->donorPermissions();
    }
    public function superPermissions()
    {
        RolePermission::factory()->create([
            "view" => true,
            "role" => RoleEnum::super,
            "permission" => "dashboard"
        ]);
        $rolePer = RolePermission::factory()->create([
            "view" => true,
            "role" => RoleEnum::super,
            "permission" => "users"
        ]);
        $this->rolePermissionSubUser($rolePer->id);
        $rolePer = RolePermission::factory()->create([
            "view" => true,
            "role" => RoleEnum::super,
            "permission" => "settings"
        ]);
        $this->rolePermissionSubSetting($rolePer->id);

        RolePermission::factory()->create([
            "view" => true,
            "role" => RoleEnum::super,
            "permission" => "reports"
        ]);
        RolePermission::factory()->create([
            "view" => true,
            "role" => RoleEnum::super,
            "permission" => "logs"
        ]);
        RolePermission::factory()->create([
            "view" => true,
            "role" => RoleEnum::super,
            "permission" => "audit"
        ]);
        $rolePer = RolePermission::factory()->create([
            "view" => true,
            "role" => RoleEnum::super,
            "permission" => "ngo"
        ]);
        $this->rolePermissionSubNgo($rolePer->id);

        RolePermission::factory()->create([
            "view" => true,
            "role" => RoleEnum::super,
            "permission" => "donor"
        ]);
        RolePermission::factory()->create([
            "view" => true,
            "role" => RoleEnum::super,
            "permission" => "projects"
        ]);
        $rolePer = RolePermission::factory()->create([
            "view" => true,
            "role" => RoleEnum::super,
            "permission" => "management/news"
        ]);
        $this->rolePermissionSubNews($rolePer->id);
        $rolePer = RolePermission::factory()->create([
            "view" => true,
            "role" => RoleEnum::super,
            "permission" => "management/about"
        ]);
        $this->rolePermissionSubAbout($rolePer->id);
    }
    public function adminPermissions()
    {
        RolePermission::factory()->create([
            "view" => true,
            "role" => RoleEnum::admin,
            "permission" => "dashboard"
        ]);
        $rolePer = RolePermission::factory()->create([
            "view" => true,
            "role" => RoleEnum::admin,
            "permission" => "users"
        ]);
        $this->rolePermissionSubUser($rolePer->id);
        $rolePer = RolePermission::factory()->create([
            "view" => true,
            "role" => RoleEnum::admin,
            "permission" => "settings"
        ]);
        $this->rolePermissionSubSetting($rolePer->id);
        RolePermission::factory()->create([
            "view" => true,
            "role" => RoleEnum::admin,
            "permission" => "reports"
        ]);
        $rolePer = RolePermission::factory()->create([
            "view" => true,
            "role" => RoleEnum::admin,
            "permission" => "ngo"
        ]);
        $this->rolePermissionSubNgo($rolePer->id);
        RolePermission::factory()->create([
            "view" => true,
            "role" => RoleEnum::admin,
            "permission" => "donor"
        ]);
        RolePermission::factory()->create([
            "view" => true,
            "role" => RoleEnum::admin,
            "permission" => "projects"
        ]);
        $rolePer = RolePermission::factory()->create([
            "view" => true,
            "role" => RoleEnum::admin,
            "permission" => "management/news"
        ]);
        $this->rolePermissionSubNews($rolePer->id);
        $rolePer = RolePermission::factory()->create([
            "view" => true,
            "role" => RoleEnum::admin,
            "permission" => "management/about"
        ]);
        $this->rolePermissionSubAbout($rolePer->id);
    }
    public function userPermissions()
    {
        RolePermission::factory()->create([
            "view" => true,
            "role" => RoleEnum::user,
            "permission" => "dashboard"
        ]);
        RolePermission::factory()->create([
            "view" => true,
            "role" => RoleEnum::user,
            "permission" => "reports"
        ]);
        $rolePer = RolePermission::factory()->create([
            "view" => true,
            "role" => RoleEnum::user,
            "permission" => "settings"
        ]);
        $this->rolePermissionSubSetting($rolePer->id);

        $rolePer = RolePermission::factory()->create([
            "view" => true,
            "role" => RoleEnum::user,
            "permission" => "ngo"
        ]);
        $this->rolePermissionSubNgo($rolePer->id);

        RolePermission::factory()->create([
            "view" => true,
            "role" => RoleEnum::user,
            "permission" => "donor"
        ]);
        RolePermission::factory()->create([
            "view" => true,
            "role" => RoleEnum::user,
            "permission" => "projects"
        ]);
        $rolePer = RolePermission::factory()->create([
            "view" => true,
            "role" => RoleEnum::user,
            "permission" => "management/news"
        ]);
        $this->rolePermissionSubNews($rolePer->id);
        $rolePer = RolePermission::factory()->create([
            "view" => true,
            "role" => RoleEnum::user,
            "permission" => "management/about"
        ]);
        $this->rolePermissionSubAbout($rolePer->id);
    }
    public function debuggerPermissions()
    {
        RolePermission::factory()->create([
            "view" => true,
            "role" => RoleEnum::debugger,
            "permission" => "dashboard"
        ]);
        RolePermission::factory()->create([
            "view" => true,
            "role" => RoleEnum::debugger,
            "permission" => "logs"
        ]);
        $rolePer = RolePermission::factory()->create([
            "view" => true,
            "role" => RoleEnum::debugger,
            "permission" => "settings"
        ]);
        $this->rolePermissionSubSetting($rolePer->id);
    }
    public function ngoPermissions()
    {
        RolePermission::factory()->create([
            "view" => true,
            "role" => RoleEnum::ngo,
            "permission" => "dashboard"
        ]);
        RolePermission::factory()->create([
            "view" => true,
            "role" => RoleEnum::ngo,
            "permission" => "projects"
        ]);
        RolePermission::factory()->create([
            "view" => true,
            "role" => RoleEnum::ngo,
            "permission" => "reports"
        ]);
        $rolePer = RolePermission::factory()->create([
            "view" => true,
            "role" => RoleEnum::ngo,
            "permission" => "settings"
        ]);
        $this->rolePermissionSubBasicSetting($rolePer->id);
    }
    public function donorPermissions()
    {
        RolePermission::factory()->create([
            "view" => true,
            "role" => RoleEnum::donor,
            "permission" => "dashboard"
        ]);
        RolePermission::factory()->create([
            "view" => true,
            "role" => RoleEnum::donor,
            "permission" => "projects"
        ]);
        RolePermission::factory()->create([
            "view" => true,
            "role" => RoleEnum::donor,
            "permission" => "ngo"
        ]);
        RolePermission::factory()->create([
            "view" => true,
            "role" => RoleEnum::donor,
            "permission" => "reports"
        ]);
        $rolePer = RolePermission::factory()->create([
            "view" => true,
            "role" => RoleEnum::donor,
            "permission" => "settings"
        ]);
        $this->rolePermissionSubBasicSetting($rolePer->id);
    }
    public function rolePermissionSubUser($role_permission_id)
    {
        foreach (SubPermissionEnum::USERS as $id => $role) {
            RolePermissionSub::factory()->create([
                "edit" => true,
                "delete" => true,
                "add" => true,
                "view" => true,
                "role_permission_id" => $role_permission_id,
                "sub_permission_id" => $id
            ]);
        }
    }
    public function rolePermissionSubNgo($role_permission_id)
    {
        foreach (SubPermissionEnum::NGO as $id => $role) {
            RolePermissionSub::factory()->create([
                "edit" => true,
                "delete" => true,
                "add" => true,
                "view" => true,
                "role_permission_id" => $role_permission_id,
                "sub_permission_id" => $id
            ]);
        }
    }

    public function rolePermissionSubBasicSetting($role_permission_id)
    {
        foreach (SubPermissionEnum::BASIC_SETTINGS as $id => $role) {
            RolePermissionSub::factory()->create([
                "edit" => true,
                "delete" => true,
                "add" => true,
                "view" => true,
                "role_permission_id" => $role_permission_id,
                "sub_permission_id" => $id
            ]);
        }
    }
    public function rolePermissionSubSetting($role_permission_id)
    {
        foreach (SubPermissionEnum::SETTINGS as $id => $role) {
            RolePermissionSub::factory()->create([
                "edit" => true,
                "delete" => true,
                "add" => true,
                "view" => true,
                "role_permission_id" => $role_permission_id,
                "sub_permission_id" => $id
            ]);
        }
    }
    public function rolePermissionSubAbout($role_permission_id)
    {
        foreach (SubPermissionEnum::ABOUT as $id => $role) {
            RolePermissionSub::factory()->create([
                "edit" => true,
                "delete" => true,
                "add" => true,
                "view" => true,
                "role_permission_id" => $role_permission_id,
                "sub_permission_id" => $id
            ]);
        }
    }
    public function rolePermissionSubNews($role_permission_id)
    {
        foreach (SubPermissionEnum::NEWS as $id => $role) {
            RolePermissionSub::factory()->create([
                "edit" => true,
                "delete" => true,
                "add" => true,
                "view" => true,
                "role_permission_id" => $role_permission_id,
                "sub_permission_id" => $id
            ]);
        }
    }
}
