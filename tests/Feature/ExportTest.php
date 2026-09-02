<?php

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(DatabaseSeeder::class);
    $this->adminUser = User::where('email', 'admin@example.com')->first();
    $this->employeeUser = User::where('email', 'employee@example.com')->first();
});

test('admin can export employee directory to CSV', function () {
    $response = $this->actingAs($this->adminUser)->get(route('admin::export.employees.csv'));

    $response->assertStatus(200);
    $response->assertHeader('content-type', 'text/csv; charset=UTF-8');
    expect($response->headers->get('content-disposition'))->toContain('hrm_employees_');

    $content = $response->streamedContent();
    expect($content)->toContain('Employee Code')
        ->and($content)->toContain('EMP-001')
        ->and($content)->toContain('admin@example.com');
});

test('admin can view printable employee master roll', function () {
    $response = $this->actingAs($this->adminUser)->get(route('admin::export.employees.print'));

    $response->assertStatus(200);
    $response->assertSee('Employee Master Register');
    $response->assertSee('HRM Enterprise Inc.');
    $response->assertSee('Prepared By:');
});

test('admin can export attendance timesheet to CSV', function () {
    $response = $this->actingAs($this->adminUser)->get(route('admin::export.attendance.csv', [
        'month' => 8,
        'year' => 2026,
    ]));

    $response->assertStatus(200);
    $response->assertHeader('content-type', 'text/csv; charset=UTF-8');
    expect($response->headers->get('content-disposition'))->toContain('hrm_attendance_');

    $content = $response->streamedContent();
    expect($content)->toContain('Employee Code')
        ->and($content)->toContain('Check In')
        ->and($content)->toContain('Working Hours');
});

test('admin can export payroll disbursement register to CSV and view printable sheet', function () {
    $csvResponse = $this->actingAs($this->adminUser)->get(route('admin::export.payroll.csv'));

    $csvResponse->assertStatus(200);
    $csvResponse->assertHeader('content-type', 'text/csv; charset=UTF-8');
    expect($csvResponse->headers->get('content-disposition'))->toContain('hrm_payroll_');

    $printResponse = $this->actingAs($this->adminUser)->get(route('admin::export.payroll.print', [
        'month' => 7,
        'year' => 2026,
    ]));

    $printResponse->assertStatus(200);
    $printResponse->assertSee('Monthly Payroll Statement');
    $printResponse->assertSee('Total Disbursed:');
});

test('admin can export leaves and expense claims to CSV', function () {
    $leavesResponse = $this->actingAs($this->adminUser)->get(route('admin::export.leaves.csv'));
    $leavesResponse->assertStatus(200);
    $leavesResponse->assertHeader('content-type', 'text/csv; charset=UTF-8');
    expect($leavesResponse->streamedContent())->toContain('Employee Code')
        ->and($leavesResponse->streamedContent())->toContain('Leave Type');

    $expensesResponse = $this->actingAs($this->adminUser)->get(route('admin::export.expenses.csv'));
    $expensesResponse->assertStatus(200);
    $expensesResponse->assertHeader('content-type', 'text/csv; charset=UTF-8');
    expect($expensesResponse->streamedContent())->toContain('Claim Code')
        ->and($expensesResponse->streamedContent())->toContain('Employee Code');
});
