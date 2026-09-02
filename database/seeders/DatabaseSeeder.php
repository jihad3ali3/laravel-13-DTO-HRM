<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\PermissionRegistrar;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Seed Roles & Permissions Matrix
        $this->call(RoleAndPermissionSeeder::class);

        // 2. Seed Enterprise Demo Data (Departments, Designations, Employees, Attendance, Leaves, Payroll, Goals, Settings)
        $this->call(EnterpriseSeeder::class);

        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }
}
