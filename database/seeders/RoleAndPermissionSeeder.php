<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Comprehensive list of all HRM permissions grouped by module
        $permissions = [
            // 1. Dashboard
            'dashboard.view',

            // 2. Employees & Staff Directory
            'employees.view',
            'employees.create',
            'employees.edit',
            'employees.delete',

            // 3. Employee Documents
            'documents.view',
            'documents.create',
            'documents.verify',
            'documents.delete',

            // 4. Attendance
            'attendance.view',
            'attendance.manage',

            // 5. Shifts & Scheduling
            'shifts.view',
            'shifts.manage',

            // 6. Leaves Management
            'leaves.view',
            'leaves.create',
            'leaves.approve',
            'leaves.manage',

            // 7. Holidays Calendar
            'holidays.view',
            'holidays.manage',

            // 8. Payroll & Salary
            'payroll.view',
            'payroll.generate',
            'payroll.pay',
            'payroll.manage',

            // 9. Expenses Claims
            'expenses.view',
            'expenses.create',
            'expenses.approve',
            'expenses.delete',

            // 10. Performance Management
            'performance.view',
            'performance.manage',
            'performance.review',

            // 11. Notice Board & Announcements
            'announcements.view',
            'announcements.manage',

            // 12. Reports & Analytics
            'reports.view',

            // 13. Settings & Organization Hierarchy
            'settings.manage',

            // 14. Roles & Access Control
            'roles.view',
            'roles.manage',

            // 15. Notifications Hub
            'notifications.view',
            'notifications.manage',

            // 16. AI HR Suite
            'ai.use',
        ];

        // Seed permissions
        foreach ($permissions as $permissionName) {
            Permission::firstOrCreate([
                'name' => $permissionName,
                'guard_name' => 'web',
            ]);
        }

        // 1. Admin Role (Super Admin - All Permissions)
        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $adminRole->syncPermissions(Permission::all());

        // 2. HR Manager Role
        $hrRole = Role::firstOrCreate(['name' => 'hr_manager', 'guard_name' => 'web']);
        $hrRole->syncPermissions([
            'dashboard.view',
            'employees.view',
            'employees.create',
            'employees.edit',
            'documents.view',
            'documents.create',
            'documents.verify',
            'attendance.view',
            'attendance.manage',
            'shifts.view',
            'shifts.manage',
            'leaves.view',
            'leaves.create',
            'leaves.approve',
            'leaves.manage',
            'holidays.view',
            'holidays.manage',
            'payroll.view',
            'payroll.generate',
            'payroll.pay',
            'payroll.manage',
            'expenses.view',
            'expenses.create',
            'expenses.approve',
            'performance.view',
            'performance.manage',
            'performance.review',
            'announcements.view',
            'announcements.manage',
            'reports.view',
            'settings.manage',
            'notifications.view',
            'notifications.manage',
            'ai.use',
        ]);

        // 3. Employee Role (Self-Service)
        $employeeRole = Role::firstOrCreate(['name' => 'employee', 'guard_name' => 'web']);
        $employeeRole->syncPermissions([
            'dashboard.view',
            'shifts.view',
            'leaves.view',
            'leaves.create',
            'holidays.view',
            'expenses.view',
            'expenses.create',
            'performance.view',
            'announcements.view',
            'documents.view',
            'documents.create',
            'attendance.view',
        ]);

        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }
}
