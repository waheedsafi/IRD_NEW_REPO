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
        $userPermission = UserPermission::factory()->create([
            "view" => true,
            "edit" => true,
            "delete" => true,
            "add" => true,
            "user_id" => RoleEnum::super->value,
            "permission" => "ngo"
        ]);
        $this->addNgoSubPermissions($userPermission, RoleEnum::super->value);
        $userPermission = UserPermission::factory()->create([
            "view" => true,
            "edit" => true,
            "delete" => true,
            "add" => true,
            "user_id" => RoleEnum::super->value,
            "permission" => "donor"
        ]);
        $this->addDonorSubPermissions($userPermission, RoleEnum::super->value);

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
            "permission" => "configurations"
        ]);
        $this->addConfigurationsubPermissions($userPermission);
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
            "permission" => "audit"
        ]);
        $userPermission = UserPermission::factory()->create([
            "view" => true,
            "edit" => true,
            "delete" => true,
            "add" => true,
            "user_id" => RoleEnum::super->value,
            "permission" => "approval"
        ]);
        $this->addApprovalSubPermissions($userPermission);
        $userPermission = UserPermission::factory()->create([
            "view" => true,
            "edit" => true,
            "delete" => true,
            "add" => true,
            "user_id" => RoleEnum::super->value,
            "permission" => "activity"
        ]);
        $this->addActivitySubPermissions($userPermission);
        $userPermission = UserPermission::factory()->create([
            "view" => true,
            "edit" => true,
            "delete" => true,
            "add" => true,
            "user_id" => RoleEnum::super->value,
            "permission" => "schedules"
        ]);
    }
    public function adminPermissions()
    {
        $userPermission = UserPermission::factory()->create([
            "view" => true,
            "edit" => true,
            "delete" => true,
            "add" => true,
            "user_id" => RoleEnum::admin->value,
            "permission" => "ngo"
        ]);
        $this->addNgoSubPermissions($userPermission, RoleEnum::admin->value);

        $userPermission = UserPermission::factory()->create([
            "view" => true,
            "edit" => true,
            "delete" => true,
            "add" => true,
            "user_id" => RoleEnum::admin->value,
            "permission" => "donor"
        ]);
        $this->addDonorSubPermissions($userPermission, RoleEnum::super->value);

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
            "permission" => "configurations"
        ]);
        $this->addConfigurationsubPermissions($userPermission);
        UserPermission::factory()->create([
            "view" => true,
            "edit" => true,
            "delete" => true,
            "add" => true,
            "user_id" => RoleEnum::admin->value,
            "permission" => "reports"
        ]);
        $userPermission = UserPermission::factory()->create([
            "view" => true,
            "edit" => true,
            "delete" => true,
            "add" => true,
            "user_id" => RoleEnum::admin->value,
            "permission" => "approval"
        ]);
        $this->addApprovalSubPermissions($userPermission);
        $userPermission = UserPermission::factory()->create([
            "view" => true,
            "edit" => true,
            "delete" => true,
            "add" => true,
            "user_id" => RoleEnum::admin->value,
            "permission" => "schedules"
        ]);
    }
    public function userPermissions()
    {
        $userPermission  = UserPermission::factory()->create([
            "view" => true,
            "edit" => true,
            "delete" => true,
            "add" => true,
            "user_id" => RoleEnum::user->value,
            "permission" => "ngo"
        ]);
        $this->addNgoSubPermissions($userPermission, RoleEnum::user->value);
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
            "permission" => "configurations"
        ]);
        $this->addConfigurationsubPermissions($userPermission);
        $userPermission = UserPermission::factory()->create([
            "view" => true,
            "edit" => true,
            "delete" => true,
            "add" => true,
            "user_id" => RoleEnum::user->value,
            "permission" => "approval"
        ]);
        $this->addApprovalSubPermissions($userPermission);
        $userPermission = UserPermission::factory()->create([
            "view" => true,
            "edit" => true,
            "delete" => true,
            "add" => true,
            "user_id" => RoleEnum::user->value,
            "permission" => "schedules"
        ]);
    }
    public function debuggerPermissions()
    {
        UserPermission::factory()->create([
            "view" => true,
            "edit" => true,
            "delete" => true,
            "add" => true,
            "user_id" => RoleEnum::debugger->value,
            "permission" => "logs"
        ]);
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
    public function addNgoSubPermissions($userPermission, $user_role)
    {
        foreach (SubPermissionEnum::NGO as $id => $role) {
            if ($id == SubPermissionEnum::ngo_update_account_password && $user_role != RoleEnum::super->value) {
                continue;
            }
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
    public function addDonorSubPermissions($userPermission, $user_role)
    {
        foreach (SubPermissionEnum::DONOR as $id => $role) {
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
    public function addConfigurationsubPermissions($userPermission)
    {
        foreach (SubPermissionEnum::CONFIGURATIONS as $id => $role) {
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
    public function addApprovalSubPermissions($userPermission)
    {
        foreach (SubPermissionEnum::APPROVALS as $id => $role) {
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
    public function addActivitySubPermissions($userPermission)
    {
        foreach (SubPermissionEnum::ACTIVITY as $id => $role) {
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
