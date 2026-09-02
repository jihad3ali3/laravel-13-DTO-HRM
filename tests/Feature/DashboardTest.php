<?php

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('admin dashboard loads with all dynamic HRM metrics and components', function () {
    $this->seed(DatabaseSeeder::class);

    $adminUser = User::where('email', 'admin@example.com')->first();

    $response = $this->actingAs($adminUser)->get(route('admin::dashboard.admin'));

    $response->assertStatus(200);

    // Header & KPIs
    $response->assertSee('Welcome back, Admin User!');
    $response->assertSee('Total Employees');
    $response->assertSee('Present Today');
    $response->assertSee('Pending Approvals');
    $response->assertSee('Payroll Disbursed');

    // Sections (passing false to disable HTML entity escaping for apostrophes)
    $response->assertSee("Today's Attendance Status", false);
    $response->assertSee('Department Distribution');
    $response->assertSee('Recent Leave Requests');
    $response->assertSee('Recent Expense Claims');
    $response->assertSee('Company Notice Board');
    $response->assertSee('Upcoming Holidays');
    $response->assertSee("Today's Check-in Log", false);

    // Dynamic Topbar Components
    $response->assertSee('HRM Quick Shortcuts');
    $response->assertSee('My Self-Service Portal');
    $response->assertSee('Sign Out');
});
