<?php

use App\Models\Attendance;
use App\Models\Department;
use App\Models\Designation;
use App\Models\Employee;
use App\Models\ExpenseClaim;
use App\Models\Leave;
use App\Models\LeaveRequest;
use App\Models\Payroll;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(DatabaseSeeder::class);
    $this->adminUser = User::where('email', 'admin@example.com')->first();
});

test('admin can create a new employee via web form', function () {
    $dept = Department::first();
    $desig = Designation::first();

    $response = $this->actingAs($this->adminUser)->post(route('admin::employees.store'), [
        'employee_code' => 'EMP-TEST-99',
        'first_name' => 'Karan',
        'last_name' => 'Mehta',
        'email' => 'karan.mehta@example.com',
        'phone' => '+91-9988776655',
        'gender' => 'male',
        'date_of_birth' => '1995-05-15',
        'joining_date' => '2026-08-01',
        'department_id' => $dept->id,
        'designation_id' => $desig->id,
        'employment_type' => 'full_time',
        'salary' => 65000.00,
        'password' => 'password123',
        'role' => 'employee',
    ]);

    $response->assertRedirect(route('admin::employees.index'));
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('employees', [
        'email' => 'karan.mehta@example.com',
        'employee_code' => 'EMP-TEST-99',
    ]);
});

test('admin can record check-in and check-out via web', function () {
    $employee = Employee::first();

    $inResponse = $this->actingAs($this->adminUser)->post(route('admin::attendance.checkIn'), [
        'employee_id' => $employee->id,
        'attendance_date' => '2026-09-02',
        'check_in' => '09:05',
    ]);

    $inResponse->assertRedirect();
    $inResponse->assertSessionHas('success');

    $att = Attendance::where('employee_id', $employee->id)->whereDate('attendance_date', now()->toDateString())->first();
    expect($att)->not->toBeNull();

    $outResponse = $this->actingAs($this->adminUser)->post(route('admin::attendance.checkOut'), [
        'employee_id' => $employee->id,
        'attendance_date' => '2026-09-02',
        'check_out' => '18:10',
    ]);

    $outResponse->assertRedirect();
    $outResponse->assertSessionHas('success');
});

test('admin can apply for leave and approve it via web', function () {
    $employee = Employee::first();
    $leaveType = Leave::first();

    $applyResponse = $this->actingAs($this->adminUser)->post(route('admin::leaves.store'), [
        'employee_id' => $employee->id,
        'leave_id' => $leaveType->id,
        'start_date' => '2026-09-10',
        'end_date' => '2026-09-12',
        'reason' => 'Attending family wedding ceremony.',
    ]);

    $applyResponse->assertRedirect();
    $applyResponse->assertSessionHas('success');

    $leaveReq = LeaveRequest::where('employee_id', $employee->id)->latest('id')->first();
    expect($leaveReq)->not->toBeNull();

    $approveResponse = $this->actingAs($this->adminUser)->patch(route('admin::leaves.process', $leaveReq->id), [
        'status' => 'approved',
    ]);

    $approveResponse->assertRedirect();
    $approveResponse->assertSessionHas('success');

    expect($leaveReq->fresh()->status)->toBe('approved');
});

test('admin can generate payroll batch and mark as paid via web', function () {
    $genResponse = $this->actingAs($this->adminUser)->post(route('admin::payroll.generate'), [
        'month' => 9,
        'year' => 2026,
    ]);

    $genResponse->assertRedirect();
    $genResponse->assertSessionHas('success');

    $payroll = Payroll::where('month', 9)->where('year', 2026)->first();
    expect($payroll)->not->toBeNull();

    $payResponse = $this->actingAs($this->adminUser)->patch(route('admin::payroll.pay', $payroll->id));
    $payResponse->assertRedirect();
    $payResponse->assertSessionHas('success');

    expect($payroll->fresh()->status)->toBe('paid');
});

test('admin can submit expense claim and process it via web', function () {
    $employee = Employee::first();

    $claimResponse = $this->actingAs($this->adminUser)->post(route('admin::expenses.store'), [
        'employee_id' => $employee->id,
        'title' => 'Hardware Adapter',
        'category' => 'equipment',
        'amount' => 1200.00,
        'expense_date' => '2026-09-01',
        'description' => 'USB-C to HDMI converter for presentation.',
    ]);

    $claimResponse->assertRedirect();
    $claimResponse->assertSessionHas('success');

    $claim = ExpenseClaim::where('employee_id', $employee->id)->latest('id')->first();
    expect($claim)->not->toBeNull();

    $processResponse = $this->actingAs($this->adminUser)->patch(route('admin::expenses.process', $claim->id), [
        'status' => 'accepted',
    ]);

    $processResponse->assertRedirect();
    $processResponse->assertSessionHas('success');

    expect($claim->fresh()->status)->toBe('accepted');
});

test('admin can post announcement and test AI tools via web', function () {
    $noticeResponse = $this->actingAs($this->adminUser)->post(route('admin::announcements.store'), [
        'title' => 'Quarterly Townhall Meeting',
        'content' => 'Join all-hands townhall this Thursday at 4 PM.',
        'priority' => 'medium',
        'start_date' => '2026-09-03',
        'is_published' => true,
    ]);

    $noticeResponse->assertRedirect();
    $noticeResponse->assertSessionHas('success');

    // AI Chat JSON Endpoint
    $aiResponse = $this->actingAs($this->adminUser)->postJson(route('admin::ai.chat'), [
        'message' => 'What is the standard working hours policy?',
    ]);

    $aiResponse->assertStatus(200);
    $aiResponse->assertJsonStructure(['response', 'confidence']);
});

test('admin can update company settings via web form', function () {
    $settingsResponse = $this->actingAs($this->adminUser)->put(route('admin::settings.company.update'), [
        'company_name' => 'Acme Global Corporation',
        'company_email' => 'contact@acmeglobal.com',
        'currency' => 'INR',
        'currency_symbol' => '₹',
        'timezone' => 'Asia/Kolkata',
        'working_hours_per_day' => 8.5,
        'working_days_per_week' => 5,
    ]);

    $settingsResponse->assertRedirect();
    $settingsResponse->assertSessionHas('success');
});

test('regular employee can submit self leave and self expense claim without admin manage rights', function () {
    $empUser = User::where('email', 'employee@example.com')->first();
    $leave = Leave::first();

    // 1. Submit self leave
    $leaveResponse = $this->actingAs($empUser)->post(route('admin::leaves.store'), [
        'leave_id' => $leave->id,
        'start_date' => '2026-09-10',
        'end_date' => '2026-09-12',
        'reason' => 'Personal urgent work',
    ]);

    $leaveResponse->assertRedirect();
    $leaveResponse->assertSessionHas('success');

    $this->assertDatabaseHas('leave_requests', [
        'employee_id' => $empUser->employee->id,
        'leave_id' => $leave->id,
        'status' => 'pending',
    ]);

    // 2. Submit self expense claim
    $expenseResponse = $this->actingAs($empUser)->post(route('admin::expenses.store'), [
        'title' => 'Client Meet Uber Cab',
        'category' => 'travel',
        'amount' => 640.00,
        'expense_date' => '2026-09-02',
        'description' => 'Airport to Office Cab',
    ]);

    $expenseResponse->assertRedirect();
    $expenseResponse->assertSessionHas('success');

    $this->assertDatabaseHas('expenses_claims', [
        'employee_id' => $empUser->employee->id,
        'title' => 'Client Meet Uber Cab',
        'amount' => 640.00,
    ]);
});
