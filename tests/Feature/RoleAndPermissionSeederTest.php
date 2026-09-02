<?php

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

uses(RefreshDatabase::class);

test('RoleAndPermissionSeeder creates all required permissions and default roles', function () {
    $this->seed(RoleAndPermissionSeeder::class);

    $expectedPermissions = [
        'dashboard.view',
        'employees.view',
        'employees.create',
        'employees.edit',
        'employees.delete',
        'documents.view',
        'documents.create',
        'documents.verify',
        'documents.delete',
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
        'expenses.delete',
        'performance.view',
        'performance.manage',
        'performance.review',
        'announcements.view',
        'announcements.manage',
        'reports.view',
        'settings.manage',
        'roles.view',
        'roles.manage',
        'notifications.view',
        'notifications.manage',
        'ai.use',
    ];

    foreach ($expectedPermissions as $perm) {
        expect(Permission::where('name', $perm)->exists())->toBeTrue();
    }

    $adminRole = Role::findByName('admin', 'web');
    expect($adminRole->permissions->count())->toBe(count($expectedPermissions));

    $hrRole = Role::findByName('hr_manager', 'web');
    expect($hrRole->hasPermissionTo('employees.create'))->toBeTrue()
        ->and($hrRole->hasPermissionTo('payroll.generate'))->toBeTrue()
        ->and($hrRole->hasPermissionTo('leaves.approve'))->toBeTrue()
        ->and($hrRole->hasPermissionTo('roles.manage'))->toBeFalse();

    $employeeRole = Role::findByName('employee', 'web');
    expect($employeeRole->hasPermissionTo('leaves.create'))->toBeTrue()
        ->and($employeeRole->hasPermissionTo('shifts.view'))->toBeTrue()
        ->and($employeeRole->hasPermissionTo('employees.delete'))->toBeFalse();
});

test('admin dashboard renders full dynamic sidebar for admin user', function () {
    $this->seed(DatabaseSeeder::class);

    $adminUser = User::where('email', 'admin@example.com')->first();

    $response = $this->actingAs($adminUser)->get(route('admin::dashboard.admin'));

    $response->assertStatus(200);
    $html = $response->getContent();

    expect(str_contains($html, 'HRM Portal'))->toBeTrue()
        ->and(str_contains($html, 'Workforce'))->toBeTrue()
        ->and(str_contains($html, 'Employee Directory'))->toBeTrue()
        ->and(str_contains($html, 'Add Employee'))->toBeTrue()
        ->and(str_contains($html, 'Payroll Batches'))->toBeTrue()
        ->and(str_contains($html, 'AI HR Suite'))->toBeTrue()
        ->and(str_contains($html, 'Administration'))->toBeTrue()
        ->and(str_contains($html, 'Roles List'))->toBeTrue()
        ->and(str_contains($html, 'Permissions Matrix'))->toBeTrue()
        ->and(str_contains($html, 'Company Profile'))->toBeTrue();
});

test('admin dashboard renders restricted dynamic sidebar for employee user', function () {
    $this->seed(DatabaseSeeder::class);

    $employeeUser = User::where('email', 'amit@example.com')->first();
    app()[PermissionRegistrar::class]->forgetCachedPermissions();

    $response = $this->actingAs($employeeUser)->get(route('admin::dashboard.admin'));

    $response->assertStatus(200);
    $html = $response->getContent();

    // Restricted menus must NOT appear in sidebar for regular employee
    expect(str_contains($html, 'Add Employee'))->toBeFalse();
    expect(str_contains($html, 'Payroll Batches'))->toBeFalse();
    expect(str_contains($html, 'AI HR Suite'))->toBeFalse();
    expect(str_contains($html, 'data-i18n="Administration"'))->toBeFalse();
    expect(str_contains($html, 'Roles List'))->toBeFalse();
    expect(str_contains($html, 'Permissions Matrix'))->toBeFalse();
    expect(str_contains($html, 'Company Profile'))->toBeFalse();
});

test('admin dashboard renders HR operations sidebar for HR manager', function () {
    $this->seed(DatabaseSeeder::class);

    $hrUser = User::where('email', 'hr@example.com')->first();
    app()[PermissionRegistrar::class]->forgetCachedPermissions();

    $response = $this->actingAs($hrUser)->get(route('admin::dashboard.admin'));

    $response->assertStatus(200);
    $html = $response->getContent();

    expect(str_contains($html, 'HRM Portal'))->toBeTrue()
        ->and(str_contains($html, 'Workforce'))->toBeTrue()
        ->and(str_contains($html, 'Employee Directory'))->toBeTrue()
        ->and(str_contains($html, 'Add Employee'))->toBeTrue()
        ->and(str_contains($html, 'Payroll Batches'))->toBeTrue()
        ->and(str_contains($html, 'AI HR Suite'))->toBeTrue()
        ->and(str_contains($html, 'Headcount Analytics'))->toBeTrue();

    // HR manager should NOT have role administration
    expect(str_contains($html, 'Roles List'))->toBeFalse()
        ->and(str_contains($html, 'Permissions Matrix'))->toBeFalse();
});
