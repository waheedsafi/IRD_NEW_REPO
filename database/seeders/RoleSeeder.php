<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Enums\RoleEnum;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::factory()->create([
            "id" => RoleEnum::super,
            "name" => "super"
        ]);
        Role::factory()->create([
            "id" => RoleEnum::admin,
            "name" => "admin"
        ]);
        Role::factory()->create([
            "id" => RoleEnum::user,
            "name" => "user"
        ]);
        Role::factory()->create([
            "id" => RoleEnum::debugger,
            "name" => "debugger"
        ]);
        Role::factory()->create([
            "id" => RoleEnum::ngo,
            "name" => "ngo"
        ]);
        Role::factory()->create([
            "id" => RoleEnum::donor,
            "name" => "donor"
        ]);
    }
}
