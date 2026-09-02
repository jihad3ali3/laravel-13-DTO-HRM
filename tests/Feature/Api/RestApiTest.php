<?php

use App\Models\Department;
use App\Models\Designation;
use App\Models\Employee;
use App\Models\Leave;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(DatabaseSeeder::class);
    $this->adminUser = User::where('email', 'admin@example.com')->first();
    $this->employeeUser = User::where('email', 'employee@example.com')->first();
    $this->employee = Employee::where('user_id', $this->employeeUser->id)->first();
});

test('API auth login returns sanctum token and user payload', function () {
    $response = $this->postJson(route('api::login'), [
        'email' => 'admin@example.com',
        'password' => 'password123',
    ]);

    $response->assertStatus(200);
    $response->assertJsonStructure([
        'data' => [
            'token',
            'user' => ['id', 'name', 'email'],
        ],
    ]);
});

test('API profile endpoint returns authenticated user details', function () {
    Sanctum::actingAs($this->adminUser);

    $response = $this->getJson(route('api::profile'));
    $response->assertStatus(200);
    $response->assertJsonFragment(['email' => 'admin@example.com']);
});

test('API attendance check-in and logs', function () {
    Sanctum::actingAs($this->adminUser);

    $logsResponse = $this->getJson(route('api::attendance.index'));
    $logsResponse->assertStatus(200);
});

test('API leaves types and requests', function () {
    Sanctum::actingAs($this->employeeUser);

    $typesResponse = $this->getJson(route('api::leaves.types'));
    $typesResponse->assertStatus(200);

    $leave = Leave::first();
    $storeResponse = $this->postJson(route('api::leaves.store'), [
        'employee_id' => $this->employee->id,
        'leave_id' => $leave->id,
        'start_date' => '2026-09-15',
        'end_date' => '2026-09-16',
        'reason' => 'Doctor appointment and medical checkup.',
    ]);

    $storeResponse->assertStatus(201);
});

test('API holidays list and show', function () {
    Sanctum::actingAs($this->employeeUser);

    $response = $this->getJson(route('api::holidays.index'));
    $response->assertStatus(200);
});

test('API expense claims submission and listing', function () {
    Sanctum::actingAs($this->employeeUser);

    $storeResponse = $this->postJson(route('api::expenses.store'), [
        'employee_id' => $this->employee->id,
        'title' => 'Conference Pass',
        'category' => 'training',
        'amount' => 4500.00,
        'expense_date' => '2026-09-01',
        'description' => 'Developer conference registration fee.',
    ]);

    $storeResponse->assertStatus(201);

    $listResponse = $this->getJson(route('api::expenses.index'));
    $listResponse->assertStatus(200);
});

test('API performance goals and reviews', function () {
    Sanctum::actingAs($this->employeeUser);

    $goalsResponse = $this->getJson(route('api::performance.goals', $this->employee->id));
    $goalsResponse->assertStatus(200);

    $reviewsResponse = $this->getJson(route('api::performance.reviews'));
    $reviewsResponse->assertStatus(200);
});

test('API announcements notice board', function () {
    Sanctum::actingAs($this->employeeUser);

    $response = $this->getJson(route('api::announcements.index'));
    $response->assertStatus(200);
});

test('API notifications and channels', function () {
    Sanctum::actingAs($this->employeeUser);

    $channelsResponse = $this->getJson(route('api::notifications.channels'));
    $channelsResponse->assertStatus(200);

    $notifResponse = $this->getJson(route('api::notifications.index'));
    $notifResponse->assertStatus(200);
});

test('API AI suite tools (chat, scan-receipt, generate-review, parse-resume, craft-announcement)', function () {
    Sanctum::actingAs($this->employeeUser);

    $chatResponse = $this->postJson(route('api::ai.chat'), [
        'message' => 'What is the leave policy?',
    ]);
    $chatResponse->assertStatus(200);
    $chatResponse->assertJsonStructure(['data' => ['response', 'confidence']]);

    $fakeReceipt = UploadedFile::fake()->create('receipt.pdf', 100, 'application/pdf');
    $receiptResponse = $this->postJson(route('api::ai.scan-receipt'), [
        'receipt' => $fakeReceipt,
    ]);
    $receiptResponse->assertStatus(200);

    $reviewResponse = $this->postJson(route('api::ai.generate-review'), [
        'employee_id' => $this->employee->id,
        'review_cycle' => 'Q3 2026',
        'rating' => 4,
    ]);
    $reviewResponse->assertStatus(200);

    $fakeResume = UploadedFile::fake()->create('resume.pdf', 100, 'application/pdf');
    $resumeResponse = $this->postJson(route('api::ai.parse-resume'), [
        'resume' => $fakeResume,
    ]);
    $resumeResponse->assertStatus(200);

    $announcementResponse = $this->postJson(route('api::ai.craft-announcement'), [
        'title' => 'Annual Strategy Meeting',
        'priority' => 'high',
        'instructions' => 'Include agenda details.',
    ]);
    $announcementResponse->assertStatus(200);
});

test('API admin dashboard metrics and reports', function () {
    Sanctum::actingAs($this->adminUser);

    $metricsResponse = $this->getJson(route('api::dashboard.metrics'));
    $metricsResponse->assertStatus(200);

    $headcountResponse = $this->getJson(route('api::reports.headcount'));
    $headcountResponse->assertStatus(200);

    $attendanceResponse = $this->getJson(route('api::reports.attendance'));
    $attendanceResponse->assertStatus(200);
});

test('API employee CRUD operations for admin', function () {
    Sanctum::actingAs($this->adminUser);

    $dept = Department::first();
    $desig = Designation::first();

    $storeResponse = $this->postJson(route('api::employees.store'), [
        'employee_code' => 'EMP-API-01',
        'first_name' => 'Suresh',
        'last_name' => 'Raina',
        'email' => 'suresh.raina@example.com',
        'phone' => '+91-9876543299',
        'gender' => 'male',
        'date_of_birth' => '1992-06-12',
        'joining_date' => '2026-08-01',
        'department_id' => $dept->id,
        'designation_id' => $desig->id,
        'employment_type' => 'full_time',
        'salary' => 75000.00,
        'password' => 'password123',
        'role' => 'employee',
    ]);

    $storeResponse->assertStatus(201);
});

test('API settings and roles management for admin', function () {
    Sanctum::actingAs($this->adminUser);

    $deptResponse = $this->getJson(route('api::settings.departments'));
    $deptResponse->assertStatus(200);

    $companyResponse = $this->getJson(route('api::settings.company'));
    $companyResponse->assertStatus(200);

    $rolesResponse = $this->getJson(route('api::roles.index'));
    $rolesResponse->assertStatus(200);
});
