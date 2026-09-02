<?php

use App\Models\Employee;
use App\Models\Payroll;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(DatabaseSeeder::class);
    $this->adminUser = User::where('email', 'admin@example.com')->first();
});

test('admin can access employee directory and onboard page', function () {
    $response = $this->actingAs($this->adminUser)->get(route('admin::employees.index'));
    $response->assertStatus(200);
    $response->assertSee('Employee Directory');

    $createResponse = $this->actingAs($this->adminUser)->get(route('admin::employees.create'));
    $createResponse->assertStatus(200);
    $createResponse->assertSee('Onboard New Employee');

    $employee = Employee::first();
    $showResponse = $this->actingAs($this->adminUser)->get(route('admin::employees.show', $employee->id));
    $showResponse->assertStatus(200);
    $showResponse->assertSee($employee->first_name);

    $editResponse = $this->actingAs($this->adminUser)->get(route('admin::employees.edit', $employee->id));
    $editResponse->assertStatus(200);
    $editResponse->assertSee('Edit Employee Profile');
});

test('admin can access documents repository', function () {
    $response = $this->actingAs($this->adminUser)->get(route('admin::documents.index'));
    $response->assertStatus(200);
    $response->assertSee('Employee Documents Repository');
});

test('admin can access attendance logs and shifts', function () {
    $attResponse = $this->actingAs($this->adminUser)->get(route('admin::attendance.index'));
    $attResponse->assertStatus(200);
    $attResponse->assertSee('Attendance Management');

    $shiftResponse = $this->actingAs($this->adminUser)->get(route('admin::shifts.index'));
    $shiftResponse->assertStatus(200);
    $shiftResponse->assertSee('Shift Management');
});

test('admin can access leave requests and holiday calendar', function () {
    $leavesResponse = $this->actingAs($this->adminUser)->get(route('admin::leaves.requests'));
    $leavesResponse->assertStatus(200);
    $leavesResponse->assertSee('Leave Applications');

    $typesResponse = $this->actingAs($this->adminUser)->get(route('admin::leaves.types'));
    $typesResponse->assertStatus(200);
    $typesResponse->assertSee('Leave Policies');

    $holidayResponse = $this->actingAs($this->adminUser)->get(route('admin::holidays.index'));
    $holidayResponse->assertStatus(200);
    $holidayResponse->assertSee('Company Holidays');
});

test('admin can access payroll batches, salary structures, payslip, and expense claims', function () {
    $payrollResponse = $this->actingAs($this->adminUser)->get(route('admin::payroll.index'));
    $payrollResponse->assertStatus(200);
    $payrollResponse->assertSee('Monthly Payroll Processing');

    $structuresResponse = $this->actingAs($this->adminUser)->get(route('admin::payroll.structures'));
    $structuresResponse->assertStatus(200);
    $structuresResponse->assertSee('Salary Structures');

    $payroll = Payroll::first();
    if ($payroll) {
        $slipResponse = $this->actingAs($this->adminUser)->get(route('admin::payroll.payslip', $payroll->id));
        $slipResponse->assertStatus(200);
        $slipResponse->assertSee('Official Payslip');
    }

    $expensesResponse = $this->actingAs($this->adminUser)->get(route('admin::expenses.index'));
    $expensesResponse->assertStatus(200);
    $expensesResponse->assertSee('Expense Claims');
});

test('admin can access performance goals and reviews', function () {
    $goalsResponse = $this->actingAs($this->adminUser)->get(route('admin::performance.goals'));
    $goalsResponse->assertStatus(200);
    $goalsResponse->assertSee('Goals &amp; Performance OKRs', false);

    $reviewsResponse = $this->actingAs($this->adminUser)->get(route('admin::performance.reviews'));
    $reviewsResponse->assertStatus(200);
    $reviewsResponse->assertSee('Appraisals &amp; Performance Reviews', false);
});

test('admin can access announcements, AI suite, and notifications hub', function () {
    $noticesResponse = $this->actingAs($this->adminUser)->get(route('admin::announcements.index'));
    $noticesResponse->assertStatus(200);
    $noticesResponse->assertSee('Company Notice Board');

    $aiResponse = $this->actingAs($this->adminUser)->get(route('admin::ai.index'));
    $aiResponse->assertStatus(200);
    $aiResponse->assertSee('AI HR Assistant Suite');

    $notifResponse = $this->actingAs($this->adminUser)->get(route('admin::notifications.index'));
    $notifResponse->assertStatus(200);
    $notifResponse->assertSee('Notifications &amp; Alerts Hub', false);
});

test('admin can access analytics reports and settings pages', function () {
    $reportsResponse = $this->actingAs($this->adminUser)->get(route('admin::reports.index'));
    $reportsResponse->assertStatus(200);
    $reportsResponse->assertSee('Reports Central');

    $headcountResponse = $this->actingAs($this->adminUser)->get(route('admin::reports.headcount'));
    $headcountResponse->assertStatus(200);
    $headcountResponse->assertSee('Headcount Analytics');

    $rolesResponse = $this->actingAs($this->adminUser)->get(route('admin::roles.index'));
    $rolesResponse->assertStatus(200);
    $rolesResponse->assertSee('Roles &amp; Access Control', false);

    $matrixResponse = $this->actingAs($this->adminUser)->get(route('admin::roles.permissions'));
    $matrixResponse->assertStatus(200);
    $matrixResponse->assertSee('Permissions Matrix');

    $deptResponse = $this->actingAs($this->adminUser)->get(route('admin::settings.departments'));
    $deptResponse->assertStatus(200);
    $deptResponse->assertSee('Organizational Departments');

    $desigResponse = $this->actingAs($this->adminUser)->get(route('admin::settings.designations'));
    $desigResponse->assertStatus(200);
    $desigResponse->assertSee('Designations &amp; Job Titles', false);

    $companyResponse = $this->actingAs($this->adminUser)->get(route('admin::settings.company'));
    $companyResponse->assertStatus(200);
    $companyResponse->assertSee('Company Profile');
});
