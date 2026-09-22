<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Roles from the LMS scope doc §2: System Admin, Admin ส่วนกลาง,
     * Admin สาขา, User. No permission list yet — controllers check roles
     * directly until finer-grained permissions are needed.
     */
    public function run(): void
    {
        foreach (['system_admin', 'central_admin', 'branch_admin', 'user'] as $role) {
            Role::firstOrCreate(['name' => $role]);
        }
    }
}
