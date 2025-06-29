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
        $rolePer = RolePermission::factory()->create([
            "role" => RoleEnum::super,
            "permission" => "users"
        ]);
        $this->rolePermissionSubUser($rolePer->id);
        $rolePer = RolePermission::factory()->create([
            "role" => RoleEnum::super,
            "permission" => "configurations"
        ]);
        $this->rolePermissionSubConfigurations($rolePer->id);

        RolePermission::factory()->create([
            "role" => RoleEnum::super,
            "permission" => "reports"
        ]);
        RolePermission::factory()->create([
            "role" => RoleEnum::super,
            "permission" => "audit"
        ]);
        $rolePer = RolePermission::factory()->create([
            "role" => RoleEnum::super,
            "permission" => "ngo"
        ]);
        $this->rolePermissionSubNgo($rolePer->id, RoleEnum::super->value);

        RolePermission::factory()->create([
            "role" => RoleEnum::super,
            "permission" => "donor"
        ]);
        RolePermission::factory()->create([
            "role" => RoleEnum::super,
            "permission" => "projects"
        ]);
        $rolePer = RolePermission::factory()->create([
            "role" => RoleEnum::super,
            "permission" => "management/news"
        ]);
        $rolePer = RolePermission::factory()->create([
            "role" => RoleEnum::super,
            "permission" => "management/about"
        ]);
        $this->rolePermissionSubAbout($rolePer->id);
        $rolePer = RolePermission::factory()->create([
            "role" => RoleEnum::super,
            "permission" => "approval"
        ]);
        $this->rolePermissionSubApproval($rolePer->id);
        $rolePer = RolePermission::factory()->create([
            "role" => RoleEnum::super,
            "permission" => "activity"
        ]);
        $this->rolePermissionSubActivity($rolePer->id);
        $rolePer = RolePermission::factory()->create([
            "role" => RoleEnum::super,
            "permission" => "schedules"
        ]);
    }
    public function adminPermissions()
    {
        $rolePer = RolePermission::factory()->create([
            "role" => RoleEnum::admin,
            "permission" => "users"
        ]);
        $this->rolePermissionSubUser($rolePer->id);
        $rolePer = RolePermission::factory()->create([
            "role" => RoleEnum::admin,
            "permission" => "configurations"
        ]);
        $this->rolePermissionSubConfigurations($rolePer->id);
        RolePermission::factory()->create([
            "role" => RoleEnum::admin,
            "permission" => "reports"
        ]);
        $rolePer = RolePermission::factory()->create([
            "role" => RoleEnum::admin,
            "permission" => "ngo"
        ]);
        $this->rolePermissionSubNgo($rolePer->id, RoleEnum::admin->value);
        RolePermission::factory()->create([
            "role" => RoleEnum::admin,
            "permission" => "donor"
        ]);
        $rolePer = RolePermission::factory()->create([
            "role" => RoleEnum::admin,
            "permission" => "management/news"
        ]);
        $rolePer = RolePermission::factory()->create([
            "role" => RoleEnum::admin,
            "permission" => "management/about"
        ]);
        $this->rolePermissionSubAbout($rolePer->id);
        $rolePer = RolePermission::factory()->create([
            "role" => RoleEnum::admin,
            "permission" => "approval"
        ]);
        $this->rolePermissionSubApproval($rolePer->id);
        $rolePer = RolePermission::factory()->create([
            "role" => RoleEnum::admin,
            "permission" => "schedules"
        ]);
    }
    public function userPermissions()
    {
        RolePermission::factory()->create([
            "role" => RoleEnum::user,
            "permission" => "reports"
        ]);
        $rolePer = RolePermission::factory()->create([
            "role" => RoleEnum::user,
            "permission" => "configurations"
        ]);
        $this->rolePermissionSubConfigurations($rolePer->id);

        $rolePer = RolePermission::factory()->create([
            "role" => RoleEnum::user,
            "permission" => "ngo"
        ]);
        $this->rolePermissionSubNgo($rolePer->id, RoleEnum::user->value);

        RolePermission::factory()->create([
            "role" => RoleEnum::user,
            "permission" => "donor"
        ]);
        $rolePer = RolePermission::factory()->create([
            "role" => RoleEnum::user,
            "permission" => "management/news"
        ]);
        $rolePer = RolePermission::factory()->create([
            "role" => RoleEnum::user,
            "permission" => "management/about"
        ]);
        $this->rolePermissionSubAbout($rolePer->id);
        $rolePer = RolePermission::factory()->create([
            "role" => RoleEnum::user,
            "permission" => "approval"
        ]);
        $this->rolePermissionSubApproval($rolePer->id);
        $rolePer = RolePermission::factory()->create([
            "role" => RoleEnum::user,
            "permission" => "schedules"
        ]);
    }
    public function debuggerPermissions()
    {
        RolePermission::factory()->create([
            "role" => RoleEnum::debugger,
            "permission" => "logs"
        ]);
    }
    public function ngoPermissions()
    {
        RolePermission::factory()->create([
            "role" => RoleEnum::ngo,
            "permission" => "projects"
        ]);
        RolePermission::factory()->create([
            "role" => RoleEnum::ngo,
            "permission" => "reports"
        ]);
    }
    public function donorPermissions()
    {
        RolePermission::factory()->create([
            "role" => RoleEnum::donor,
            "permission" => "projects"
        ]);
        RolePermission::factory()->create([
            "role" => RoleEnum::donor,
            "permission" => "ngo"
        ]);
        RolePermission::factory()->create([
            "role" => RoleEnum::donor,
            "permission" => "reports"
        ]);
    }
    public function rolePermissionSubUser($role_permission_id)
    {
        foreach (SubPermissionEnum::USERS as $id => $role) {
            RolePermissionSub::factory()->create([
                "role_permission_id" => $role_permission_id,
                "sub_permission_id" => $id
            ]);
        }
    }
    public function rolePermissionSubNgo($role_permission_id, $user_role)
    {
        foreach (SubPermissionEnum::NGO as $id => $role) {
            if ($id == SubPermissionEnum::ngo_status->value && $user_role != RoleEnum::super->value) {
                continue;
            }
            if ($id == SubPermissionEnum::ngo_agreement_status->value && $user_role != RoleEnum::super->value) {
                continue;
            }
            if ($id == SubPermissionEnum::ngo_update_account_password->value && $user_role != RoleEnum::super->value) {
                continue;
            }
            RolePermissionSub::factory()->create([
                "role_permission_id" => $role_permission_id,
                "sub_permission_id" => $id
            ]);
        }
    }

    public function rolePermissionSubConfigurations($role_permission_id)
    {
        foreach (SubPermissionEnum::CONFIGURATIONS as $id => $role) {
            RolePermissionSub::factory()->create([
                "role_permission_id" => $role_permission_id,
                "sub_permission_id" => $id
            ]);
        }
    }
    public function rolePermissionSubAbout($role_permission_id)
    {
        foreach (SubPermissionEnum::ABOUT as $id => $role) {
            RolePermissionSub::factory()->create([
                "role_permission_id" => $role_permission_id,
                "sub_permission_id" => $id
            ]);
        }
    }
    public function rolePermissionSubApproval($role_permission_id)
    {
        foreach (SubPermissionEnum::APPROVALS as $id => $role) {
            RolePermissionSub::factory()->create([
                "role_permission_id" => $role_permission_id,
                "sub_permission_id" => $id
            ]);
        }
    }
    public function rolePermissionSubActivity($role_permission_id)
    {
        foreach (SubPermissionEnum::ACTIVITY as $id => $role) {
            RolePermissionSub::factory()->create([
                "role_permission_id" => $role_permission_id,
                "sub_permission_id" => $id
            ]);
        }
    }
}
