<?php

use App\Models\Announcement;
use App\Models\Department;
use App\Models\Designation;
use App\Models\Employee;
use App\Models\ExpenseClaim;
use App\Models\Holiday;
use App\Models\Leave;
use App\Models\LeaveRequest;
use App\Models\Payroll;
use App\Models\PerformanceGoal;
use App\Models\Shift;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Set up basic permissions
    Permission::firstOrCreate(['name' => 'employees.create', 'guard_name' => 'web']);
    Permission::firstOrCreate(['name' => 'employees.edit', 'guard_name' => 'web']);
    Permission::firstOrCreate(['name' => 'employees.delete', 'guard_name' => 'web']);
    Permission::firstOrCreate(['name' => 'leaves.approve', 'guard_name' => 'web']);
    Permission::firstOrCreate(['name' => 'expenses.approve', 'guard_name' => 'web']);
    Permission::firstOrCreate(['name' => 'performance.manage', 'guard_name' => 'web']);
    Permission::firstOrCreate(['name' => 'shifts.manage', 'guard_name' => 'web']);
    Permission::firstOrCreate(['name' => 'shifts.view', 'guard_name' => 'web']);
    Permission::firstOrCreate(['name' => 'settings.manage', 'guard_name' => 'web']);
    Permission::firstOrCreate(['name' => 'payroll.generate', 'guard_name' => 'web']);
    Permission::firstOrCreate(['name' => 'payroll.view', 'guard_name' => 'web']);
    Permission::firstOrCreate(['name' => 'payroll.pay', 'guard_name' => 'web']);
    Permission::firstOrCreate(['name' => 'reports.view', 'guard_name' => 'web']);
    Permission::firstOrCreate(['name' => 'announcements.manage', 'guard_name' => 'web']);

    $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
    $adminRole->syncPermissions(Permission::all());

    $employeeRole = Role::firstOrCreate(['name' => 'employee', 'guard_name' => 'web']);

    $this->adminUser = User::factory()->create([
        'name' => 'Admin User',
        'email' => 'admin@example.com',
    ]);
    $this->adminUser->assignRole('admin');

    $this->empUser = User::factory()->create([
        'name' => 'Employee User',
        'email' => 'emp@example.com',
    ]);
    $this->empUser->assignRole('employee');

    $this->dept = Department::create(['name' => 'Engineering', 'code' => 'ENG']);
    $this->desg = Designation::create(['department_id' => $this->dept->id, 'name' => 'Software Engineer']);

    $this->employee = Employee::create([
        'user_id' => $this->empUser->id,
        'employee_code' => 'EMP-001',
        'first_name' => 'Employee',
        'last_name' => 'User',
        'email' => 'emp@example.com',
        'gender' => 'male',
        'joining_date' => '2026-01-01',
        'department_id' => $this->dept->id,
        'designation_id' => $this->desg->id,
        'salary' => 50000,
    ]);
});

test('can manage departments and designations via Settings API', function () {
    Sanctum::actingAs($this->adminUser);

    // Create Department
    $response = $this->postJson('/api/v1/settings/departments', [
        'name' => 'Human Resources',
        'code' => 'HR',
        'description' => 'HR Department',
    ]);
    $response->assertStatus(201); // created
    $this->assertDatabaseHas('departments', ['code' => 'HR']);

    // List Departments
    $response = $this->getJson('/api/v1/settings/departments');
    $response->assertStatus(200);

    // Create Designation
    $dept = Department::where('code', 'HR')->first();
    $response = $this->postJson('/api/v1/settings/designations', [
        'department_id' => $dept->id,
        'name' => 'HR Manager',
    ]);
    $response->assertStatus(201);
    $this->assertDatabaseHas('designations', ['name' => 'HR Manager']);
});

test('can CRUD employees via Directory API', function () {
    Sanctum::actingAs($this->adminUser);

    // Create Employee
    $response = $this->postJson('/api/v1/employees', [
        'employee_code' => 'EMP-002',
        'first_name' => 'John',
        'last_name' => 'Doe',
        'email' => 'john.doe@example.com',
        'gender' => 'male',
        'joining_date' => '2026-02-01',
        'employment_type' => 'full_time',
        'salary' => 60000,
        'password' => 'secret1234',
        'role' => 'employee',
    ]);
    $response->assertStatus(201);
    $this->assertDatabaseHas('employees', ['employee_code' => 'EMP-002']);
    $this->assertDatabaseHas('users', ['email' => 'john.doe@example.com']);

    // List Employees
    $response = $this->getJson('/api/v1/employees');
    $response->assertStatus(200);

    // Show Employee
    $emp = Employee::where('employee_code', 'EMP-002')->first();
    $response = $this->getJson("/api/v1/employees/{$emp->id}");
    $response->assertStatus(200);

    // Update Employee
    $response = $this->putJson("/api/v1/employees/{$emp->id}", [
        'first_name' => 'Johnny',
        'last_name' => 'Doe',
        'gender' => 'male',
        'employment_type' => 'full_time',
        'salary' => 65000,
    ]);
    $response->assertStatus(200);
    $this->assertDatabaseHas('employees', ['first_name' => 'Johnny']);

    // Delete Employee
    $response = $this->deleteJson("/api/v1/employees/{$emp->id}");
    $response->assertStatus(200);
    $this->assertDatabaseMissing('employees', ['employee_code' => 'EMP-002']);
});

test('can submit and process leave requests', function () {
    $leaveType = Leave::create([
        'name' => 'Sick Leave',
        'code' => 'SL',
        'max_day_per_year' => 10,
        'is_paid' => true,
    ]);

    Sanctum::actingAs($this->empUser);

    // Get Leave Types
    $response = $this->getJson('/api/v1/leaves/types');
    $response->assertStatus(200);

    // Submit Leave Request
    $response = $this->postJson('/api/v1/leaves/requests', [
        'leave_id' => $leaveType->id,
        'start_date' => '2026-09-01',
        'end_date' => '2026-09-03',
        'reason' => 'Fever',
    ]);
    $response->assertStatus(201);
    $this->assertDatabaseHas('leave_requests', ['reason' => 'Fever']);

    // List Requests
    $response = $this->getJson('/api/v1/leaves/requests');
    $response->assertStatus(200);

    // Check Balance
    $response = $this->getJson("/api/v1/leaves/balance/{$this->employee->id}");
    $response->assertStatus(200);

    // Process Request (Approve as Admin)
    Sanctum::actingAs($this->adminUser);
    $req = LeaveRequest::first();
    $response = $this->patchJson("/api/v1/leaves/requests/{$req->id}/status", [
        'status' => 'approved',
    ]);
    $response->assertStatus(200);
    $this->assertDatabaseHas('leave_requests', ['status' => 'approved']);
});

test('can manage holidays calendar', function () {
    Sanctum::actingAs($this->adminUser);

    // Create Holiday
    $response = $this->postJson('/api/v1/holidays', [
        'name' => 'New Year',
        'holiday_date' => '2026-01-01',
        'type' => 'public',
        'description' => 'New Year Day',
    ]);
    $response->assertStatus(201);
    $this->assertDatabaseHas('holidays', ['name' => 'New Year']);

    // List Holidays
    $response = $this->getJson('/api/v1/holidays');
    $response->assertStatus(200);
});

test('can submit and process expense claims', function () {
    Sanctum::actingAs($this->empUser);

    // Submit Claim
    $response = $this->postJson('/api/v1/expenses', [
        'title' => 'Client Lunch',
        'category' => 'meals',
        'amount' => 1200,
        'expense_date' => '2026-08-20',
        'description' => 'Dinner with client XYZ',
    ]);
    $response->assertStatus(201);
    $this->assertDatabaseHas('expenses_claims', ['title' => 'Client Lunch']);

    // Process Claim (Approve as Admin)
    Sanctum::actingAs($this->adminUser);
    $claim = ExpenseClaim::first();
    $response = $this->patchJson("/api/v1/expenses/{$claim->id}/status", [
        'status' => 'accepted',
    ]);
    $response->assertStatus(200);
    $this->assertDatabaseHas('expenses_claims', ['status' => 'accepted']);
});

test('can manage performance goals and reviews', function () {
    Sanctum::actingAs($this->adminUser);

    // Assign Goal
    $response = $this->postJson('/api/v1/performance/goals', [
        'employee_id' => $this->employee->id,
        'title' => 'Complete System Integration',
        'start_date' => '2026-08-01',
        'due_date' => '2026-09-30',
        'priority' => 'high',
    ]);
    $response->assertStatus(201);
    $this->assertDatabaseHas('performance_goals', ['title' => 'Complete System Integration']);

    // Update Goal Progress (as Employee)
    Sanctum::actingAs($this->empUser);
    $goal = PerformanceGoal::first();
    $response = $this->patchJson("/api/v1/performance/goals/{$goal->id}/progress", [
        'progress' => 50,
    ]);
    $response->assertStatus(200);
    $this->assertDatabaseHas('performance_goals', ['progress' => 50, 'status' => 'in_progress']);

    // Create Review (as Admin)
    Sanctum::actingAs($this->adminUser);
    $response = $this->postJson('/api/v1/performance/reviews', [
        'employee_id' => $this->employee->id,
        'review_cycle' => 'Q3 2026',
        'rating' => 4,
        'strengths' => 'Great communication skills',
        'status' => 'submitted',
    ]);
    $response->assertStatus(201);
    $this->assertDatabaseHas('performance_reviews', ['rating' => 4]);
});

test('can manage shift rosters', function () {
    Sanctum::actingAs($this->adminUser);

    // Create Shift
    $response = $this->postJson('/api/v1/shifts', [
        'name' => 'Night Shift',
        'shift_code' => 'NIGHT',
        'start_time' => '22:00',
        'end_time' => '06:00',
    ]);
    $response->assertStatus(201);
    $this->assertDatabaseHas('shifts', ['shift_code' => 'NIGHT']);

    // Assign Shift
    $shift = Shift::first();
    $response = $this->postJson('/api/v1/shifts/assign', [
        'employee_id' => $this->employee->id,
        'shift_id' => $shift->id,
        'start_date' => '2026-09-01',
        'end_date' => '2026-09-30',
    ]);
    $response->assertStatus(200);
    $this->assertDatabaseHas('employee_shifts', ['employee_id' => $this->employee->id]);
});

test('can trigger mock AI assistant features', function () {
    Sanctum::actingAs($this->empUser);

    // AI Chat
    $response = $this->postJson('/api/v1/ai/chat', [
        'message' => 'What is the leave policy?',
    ]);
    $response->assertStatus(200)
        ->assertJsonPath('data.confidence', 0.95);

    // AI Craft Announcement
    $response = $this->postJson('/api/v1/ai/craft-announcement', [
        'title' => 'Office Renovation',
        'priority' => 'medium',
    ]);
    $response->assertStatus(200)
        ->assertJsonStructure(['data' => ['subject', 'content']]);
});

test('can manage payroll batches and salary structures', function () {
    Sanctum::actingAs($this->adminUser);

    // Update Salary Structure
    $response = $this->postJson('/api/v1/payroll/salary-structure', [
        'employee_id' => $this->employee->id,
        'basic_salary' => 50000,
        'hra' => 15000,
        'medical_allowance' => 3000,
        'transport_allowance' => 2000,
        'special_allowance' => 5000,
        'tax_deduction' => 6000,
        'provident_fund' => 5000,
        'other_deductions' => 1000,
    ]);
    $response->assertStatus(200);
    $this->assertDatabaseHas('salary_structures', ['employee_id' => $this->employee->id, 'net_salary' => 63000]);

    // Generate Payroll
    $response = $this->postJson('/api/v1/payroll/generate', [
        'month' => 8,
        'year' => 2026,
    ]);
    $response->assertStatus(200);
    $this->assertDatabaseHas('payrolls', ['employee_id' => $this->employee->id, 'net_salary' => 63000]);
});
