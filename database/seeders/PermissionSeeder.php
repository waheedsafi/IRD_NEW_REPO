<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Icons
        $dashboard = 'public/icons/home.svg';
        $users = 'public/icons/users-group.svg';
        $chart = 'public/icons/chart.svg';
        $settings = 'public/icons/settings.svg';
        $logs = 'public/icons/logs.svg';
        $audit = 'public/icons/audits.svg';
        $projects = 'public/icons/projects.svg';
        $ngo = 'public/icons/ngo.svg';
        $donor = 'public/icons/donor.svg';
        $management = 'public/icons/management.svg';
        $managementNews = 'public/icons/management-news.svg';
        $approval = 'public/icons/approval.svg';

        Permission::factory()->create([
            "name" => "dashboard",
            "icon" => $dashboard,
            "priority" => 1,
        ]);
        Permission::factory()->create([
            "name" => "ngo",
            "icon" => $ngo,
            "priority" => 2,
        ]);
        Permission::factory()->create([
            "name" => "donor",
            "icon" => $donor,
            "priority" => 3,
        ]);
        Permission::factory()->create([
            "name" => "projects",
            "icon" => $projects,
            "priority" => 4,
        ]);
        Permission::factory()->create([
            "name" => "management/news",
            "icon" => $management,
            "priority" => 5,
        ]);
        Permission::factory()->create([
            "name" => "management/about",
            "icon" => $managementNews,
            "priority" => 5,
        ]);
        Permission::factory()->create([
            "name" => "users",
            "icon" => $users,
            "priority" => 6,
        ]);
        Permission::factory()->create([
            "name" => "reports",
            "icon" => $chart,
            "priority" => 7,
        ]);
        Permission::factory()->create([
            "name" => "logs",
            "icon" => $logs,
            "priority" => 8,
        ]);
        Permission::factory()->create([
            "name" => "audit",
            "icon" => $audit,
            "priority" => 9,
        ]);
        Permission::factory()->create([
            "name" => "settings",
            "icon" => $settings,
            "priority" => 10,
        ]);
        Permission::factory()->create([
            "name" => "approval",
            "icon" => $approval,
            "priority" => 11,
        ]);
    }
}
