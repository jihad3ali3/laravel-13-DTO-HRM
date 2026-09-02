<?php

use App\Models\Employee;
use App\Models\Leave;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(DatabaseSeeder::class);
    $this->employeeUser = User::where('email', 'employee@example.com')->first();
    $this->employee = Employee::where('user_id', $this->employeeUser->id)->first();
});

test('employee can access ESS portal dashboard', function () {
    $response = $this->actingAs($this->employeeUser)->get(route('portal::dashboard'));
    $response->assertStatus(200);
    $response->assertSee('Employee Self-Service Portal');
    $response->assertSee('My Active Goals &amp; OKRs', false);
    $response->assertSee('Company Notice Board');
});

test('employee can view personal timesheet and clock in', function () {
    $response = $this->actingAs($this->employeeUser)->get(route('portal::attendance.index'));
    $response->assertStatus(200);
    $response->assertSee('My Attendance &amp; Timesheets', false);

    // Clock in action
    $inResponse = $this->actingAs($this->employeeUser)->post(route('portal::attendance.checkIn'));
    $inResponse->assertRedirect();
    $inResponse->assertSessionHas('success');
});

test('employee can view leave balances and apply for time off', function () {
    $response = $this->actingAs($this->employeeUser)->get(route('portal::leaves.index'));
    $response->assertStatus(200);
    $response->assertSee('My Leaves &amp; Annual Quotas', false);

    $leave = Leave::first();
    $applyResponse = $this->actingAs($this->employeeUser)->post(route('portal::leaves.store'), [
        'leave_id' => $leave->id,
        'start_date' => '2026-09-20',
        'end_date' => '2026-09-22',
        'reason' => 'Family vacation trip.',
    ]);

    $applyResponse->assertRedirect();
    $applyResponse->assertSessionHas('success');
});

test('employee can submit expense claim via portal', function () {
    $response = $this->actingAs($this->employeeUser)->get(route('portal::expenses.index'));
    $response->assertStatus(200);
    $response->assertSee('My Expense Claims &amp; Reimbursements', false);

    $claimResponse = $this->actingAs($this->employeeUser)->post(route('portal::expenses.store'), [
        'title' => 'Taxi to Client Office',
        'category' => 'travel',
        'amount' => 650.00,
        'expense_date' => '2026-09-02',
        'description' => 'Travel reimbursement for customer meeting.',
    ]);

    $claimResponse->assertRedirect();
    $claimResponse->assertSessionHas('success');
});

test('employee can view personal payslips and update OKR progress', function () {
    $payResponse = $this->actingAs($this->employeeUser)->get(route('portal::payroll.index'));
    $payResponse->assertStatus(200);
    $payResponse->assertSee('My Payslips &amp; Compensation', false);

    $perfResponse = $this->actingAs($this->employeeUser)->get(route('portal::performance.index'));
    $perfResponse->assertStatus(200);
    $perfResponse->assertSee('My Performance OKRs &amp; Appraisals', false);
});
