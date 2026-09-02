<?php

namespace Database\Seeders;

use App\Models\Announcement;
use App\Models\Attendance;
use App\Models\CompanySetting;
use App\Models\Department;
use App\Models\Designation;
use App\Models\Employee;
use App\Models\EmployeeShift;
use App\Models\ExpenseClaim;
use App\Models\Holiday;
use App\Models\Leave;
use App\Models\LeaveRequest;
use App\Models\Notification;
use App\Models\Payroll;
use App\Models\PerformanceGoal;
use App\Models\PerformanceReview;
use App\Models\SalaryStructure;
use App\Models\Shift;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Seed Roles & Permissions
        $permissions = [
            'employees.create',
            'employees.edit',
            'employees.delete',
            'leaves.approve',
            'expenses.approve',
            'performance.manage',
            'shifts.manage',
            'shifts.view',
            'settings.manage',
            'payroll.generate',
            'payroll.view',
            'payroll.pay',
            'reports.view',
            'announcements.manage',
        ];

        foreach ($permissions as $permissionName) {
            Permission::firstOrCreate(['name' => $permissionName, 'guard_name' => 'web']);
        }

        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $adminRole->syncPermissions(Permission::all());

        $hrRole = Role::firstOrCreate(['name' => 'hr_manager', 'guard_name' => 'web']);
        $hrRole->syncPermissions([
            'employees.create',
            'employees.edit',
            'leaves.approve',
            'expenses.approve',
            'performance.manage',
            'shifts.view',
            'payroll.generate',
            'payroll.view',
            'reports.view',
            'announcements.manage',
        ]);

        $employeeRole = Role::firstOrCreate(['name' => 'employee', 'guard_name' => 'web']);
        $employeeRole->syncPermissions([
            'shifts.view',
        ]);

        // 2. Seed Departments & Designations
        $engineering = Department::create([
            'name' => 'Engineering',
            'code' => 'ENG',
            'description' => 'Software engineering and development division',
            'is_active' => true,
        ]);

        $hrDept = Department::create([
            'name' => 'Human Resources',
            'code' => 'HRD',
            'description' => 'People management and recruitment',
            'is_active' => true,
        ]);

        $finance = Department::create([
            'name' => 'Finance & Accounts',
            'code' => 'FIN',
            'description' => 'Financial strategy, accounts, and payouts',
            'is_active' => true,
        ]);

        $se = Designation::create([
            'department_id' => $engineering->id,
            'name' => 'Software Engineer',
            'description' => 'Builds software products',
            'is_active' => true,
        ]);

        $tl = Designation::create([
            'department_id' => $engineering->id,
            'name' => 'Tech Lead',
            'description' => 'Leads engineering projects',
            'is_active' => true,
        ]);

        $hrm = Designation::create([
            'department_id' => $hrDept->id,
            'name' => 'HR Manager',
            'description' => 'Manages HR operations',
            'is_active' => true,
        ]);

        $fa = Designation::create([
            'department_id' => $finance->id,
            'name' => 'Financial Analyst',
            'description' => 'Financial reporting and analysis',
            'is_active' => true,
        ]);

        // 3. Seed Users & Employees
        $usersData = [
            [
                'name' => 'Admin User',
                'email' => 'admin@example.com',
                'role' => 'admin',
                'employee_code' => 'EMP-001',
                'first_name' => 'System',
                'last_name' => 'Administrator',
                'phone' => '+91-9999999999',
                'gender' => 'male',
                'joining_date' => '2025-01-01',
                'department_id' => $engineering->id,
                'designation_id' => $tl->id,
                'salary' => 120000,
            ],
            [
                'name' => 'HR Manager',
                'email' => 'hr@example.com',
                'role' => 'hr_manager',
                'employee_code' => 'EMP-002',
                'first_name' => 'Priya',
                'last_name' => 'Sharma',
                'phone' => '+91-9888888888',
                'gender' => 'female',
                'joining_date' => '2025-06-01',
                'department_id' => $hrDept->id,
                'designation_id' => $hrm->id,
                'salary' => 80000,
            ],
            [
                'name' => 'Amit Kumar',
                'email' => 'amit@example.com',
                'role' => 'employee',
                'employee_code' => 'EMP-003',
                'first_name' => 'Amit',
                'last_name' => 'Kumar',
                'phone' => '+91-9777777777',
                'gender' => 'male',
                'joining_date' => '2026-01-15',
                'department_id' => $engineering->id,
                'designation_id' => $se->id,
                'salary' => 60000,
            ],
            [
                'name' => 'Rohit Singh',
                'email' => 'rohit@example.com',
                'role' => 'employee',
                'employee_code' => 'EMP-004',
                'first_name' => 'Rohit',
                'last_name' => 'Singh',
                'phone' => '+91-9666666666',
                'gender' => 'male',
                'joining_date' => '2026-02-01',
                'department_id' => $finance->id,
                'designation_id' => $fa->id,
                'salary' => 55000,
            ],
        ];

        $employees = [];

        foreach ($usersData as $data) {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make('password'),
            ]);

            $user->assignRole($data['role']);

            $employee = Employee::create([
                'user_id' => $user->id,
                'employee_code' => $data['employee_code'],
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'gender' => $data['gender'],
                'joining_date' => $data['joining_date'],
                'department_id' => $data['department_id'],
                'designation_id' => $data['designation_id'],
                'salary' => $data['salary'],
                'address' => '123, Tech Boulevard, Sector 62, Noida, India',
                'emergency_contact' => '+91-9000000000',
                'is_active' => true,
            ]);

            $employees[] = $employee;

            // Seed Salary Structure for each employee
            SalaryStructure::create([
                'employee_id' => $employee->id,
                'basic_salary' => $data['salary'] * 0.5,
                'hra' => $data['salary'] * 0.2,
                'medical_allowance' => 2500,
                'transport_allowance' => 1600,
                'special_allowance' => ($data['salary'] * 0.3) - 4100,
                'tax_deduction' => $data['salary'] * 0.1,
                'provident_fund' => $data['salary'] * 0.08,
                'other_deductions' => 0,
                'net_salary' => $data['salary'] - ($data['salary'] * 0.18),
            ]);
        }

        // 4. Seed Shifts & Assign them
        $dayShift = Shift::create([
            'name' => 'Morning Shift',
            'shift_code' => 'DAY',
            'start_time' => '09:00:00',
            'end_time' => '18:00:00',
            'mark_late_after_minutes' => 15,
            'total_hours' => 9.0,
            'is_active' => true,
        ]);

        $nightShift = Shift::create([
            'name' => 'Night Shift',
            'shift_code' => 'NIGHT',
            'start_time' => '22:00:00',
            'end_time' => '06:00:00',
            'mark_late_after_minutes' => 15,
            'total_hours' => 8.0,
            'is_active' => true,
        ]);

        foreach ($employees as $employee) {
            EmployeeShift::create([
                'employee_id' => $employee->id,
                'shift_id' => $dayShift->id,
                'start_date' => '2026-01-01',
                'end_date' => '2026-12-31',
                'is_active' => true,
            ]);
        }

        // 5. Seed Attendance Logs (for last 15 weekdays)
        $date = Carbon::now()->subDays(20);
        for ($i = 0; $i < 20; $i++) {
            $currentDate = $date->copy()->addDays($i);
            if ($currentDate->isWeekend() || $currentDate->gt(Carbon::today())) {
                continue;
            }

            foreach ($employees as $employee) {
                // Introduce some variance (some late, some half-day, some present)
                $rand = rand(1, 10);
                if ($rand === 10) {
                    // Absent or on leave
                    continue;
                }

                $checkInTime = $rand === 9 ? '09:45:00' : '09:02:00';
                $checkOutTime = $rand === 9 ? '18:00:00' : ($rand === 8 ? '14:00:00' : '18:05:00');
                $status = $rand === 9 ? Attendance::STATUS_LATE : ($rand === 8 ? Attendance::STATUS_HALF_DAY : Attendance::STATUS_PRESENT);

                Attendance::create([
                    'employee_id' => $employee->id,
                    'attendance_date' => $currentDate->toDateString(),
                    'check_in' => $checkInTime,
                    'check_out' => $checkOutTime,
                    'status' => $status,
                    'working_hours' => $rand === 8 ? 4.5 : 9.0,
                    'notes' => $rand === 9 ? 'Delayed due to traffic' : null,
                ]);
            }
        }

        // 6. Seed Leaves & Leave Requests
        $sickLeave = Leave::create([
            'name' => 'Sick Leave',
            'code' => 'SL',
            'max_day_per_year' => 12,
            'is_paid' => true,
            'is_active' => true,
        ]);

        $casualLeave = Leave::create([
            'name' => 'Casual Leave',
            'code' => 'CL',
            'max_day_per_year' => 10,
            'is_paid' => true,
            'is_active' => true,
        ]);

        // Create Leave Requests
        LeaveRequest::create([
            'employee_id' => $employees[2]->id, // Amit
            'leave_id' => $casualLeave->id,
            'start_date' => Carbon::now()->addDays(5)->toDateString(),
            'end_date' => Carbon::now()->addDays(7)->toDateString(),
            'status' => LeaveRequest::STATUS_PENDING,
            'reason' => 'Family wedding function',
        ]);

        LeaveRequest::create([
            'employee_id' => $employees[3]->id, // Rohit
            'leave_id' => $sickLeave->id,
            'start_date' => Carbon::now()->subDays(10)->toDateString(),
            'end_date' => Carbon::now()->subDays(9)->toDateString(),
            'status' => LeaveRequest::STATUS_APPROVED,
            'reason' => 'Severe seasonal viral fever',
            'actioned_by' => $usersData[1]['employee_code'] === 'EMP-002' ? 2 : 1, // Actioned by HR / Admin
            'actioned_at' => Carbon::now()->subDays(11),
        ]);

        // 7. Seed Holidays Calendar
        Holiday::create([
            'name' => 'New Year Day',
            'holiday_date' => '2026-01-01',
            'type' => 'public',
            'holiday_year' => 2026,
            'description' => 'Global New Year celebration day',
        ]);

        Holiday::create([
            'name' => 'Independence Day',
            'holiday_date' => '2026-08-15',
            'type' => 'public',
            'holiday_year' => 2026,
            'description' => 'Indian Independence Day',
        ]);

        Holiday::create([
            'name' => 'Christmas Day',
            'holiday_date' => '2026-12-25',
            'type' => 'public',
            'holiday_year' => 2026,
            'description' => 'Christmas Holiday celebration',
        ]);

        // 8. Seed Expense Claims
        ExpenseClaim::create([
            'employee_id' => $employees[2]->id, // Amit
            'claim_code' => 'EXP-LUNCH-'.strtoupper(uniqid()),
            'title' => 'Client Team Lunch',
            'category' => 'meals',
            'amount' => 3200.00,
            'expense_date' => Carbon::now()->subDays(5)->toDateString(),
            'description' => 'Took client tech team out for business dinner',
            'status' => ExpenseClaim::STATUS_PENDING,
        ]);

        ExpenseClaim::create([
            'employee_id' => $employees[3]->id, // Rohit
            'claim_code' => 'EXP-INTERNET-'.strtoupper(uniqid()),
            'title' => 'Broadband WiFi Bill',
            'category' => 'internet',
            'amount' => 1500.00,
            'expense_date' => Carbon::now()->subDays(20)->toDateString(),
            'description' => 'Broadband WFH reimbursement claim',
            'status' => ExpenseClaim::STATUS_REIMBURSED,
            'actioned_by' => 1,
            'actioned_at' => Carbon::now()->subDays(18),
            'reimbursed_date' => Carbon::now()->subDays(15)->toDateString(),
        ]);

        // 9. Seed Performance Goals & Reviews
        PerformanceGoal::create([
            'employee_id' => $employees[2]->id, // Amit
            'title' => 'Implement API Testing Suite',
            'description' => 'Deliver comprehensive feature tests hitting 80% code coverage.',
            'start_date' => '2026-08-01',
            'due_date' => '2026-08-31',
            'progress' => 60,
            'priority' => 'high',
            'status' => 'in_progress',
        ]);

        PerformanceGoal::create([
            'employee_id' => $employees[3]->id, // Rohit
            'title' => 'Close Audits for FY25',
            'description' => 'Complete internal reconciliations and finalize corporate taxes filings.',
            'start_date' => '2026-07-01',
            'due_date' => '2026-09-30',
            'progress' => 20,
            'priority' => 'medium',
            'status' => 'in_progress',
        ]);

        PerformanceReview::create([
            'employee_id' => $employees[2]->id, // Amit
            'reviewer_id' => 1, // Admin reviewer
            'review_cycle' => 'Mid-Year 2026',
            'reviewed_at' => Carbon::now()->subDays(30),
            'rating' => 4,
            'strengths' => 'Excellent core development speed and clean code architectures.',
            'area_of_improvements' => 'Focus on documenting test suites and database migration dependencies.',
            'status' => 'submitted',
        ]);

        // 10. Seed Announcements
        Announcement::create([
            'title' => 'New Leave Policy Guidelines',
            'content' => 'Please note that all casual leaves must be applied at least 2 days in advance starting next month. Urgent emergency sick leaves remain unchanged.',
            'priority' => 'medium',
            'is_pinned' => true,
            'start_date' => '2026-08-20',
            'is_published' => true,
            'created_by' => 1,
        ]);

        Announcement::create([
            'title' => 'Acme Annual Picnic Rescheduled',
            'content' => 'Due to weather alerts, the annual corporate outing has been shifted to September 12th.',
            'priority' => 'low',
            'is_pinned' => false,
            'start_date' => '2026-08-23',
            'is_published' => true,
            'created_by' => 1,
        ]);

        // 11. Seed Notifications
        Notification::create([
            'employee_id' => $employees[2]->id, // Amit
            'type' => 'leave',
            'title' => 'Leave Approved',
            'message' => 'Your leave request for SL (2026-08-13) has been approved by Priya Sharma.',
            'channel' => 'database',
            'status' => 'read',
            'read_at' => Carbon::now()->subDays(10),
        ]);

        Notification::create([
            'employee_id' => $employees[2]->id, // Amit
            'type' => 'announcement',
            'title' => 'New Notice Board Post',
            'message' => 'HR Manager Priya Sharma published: New Leave Policy Guidelines.',
            'channel' => 'database',
            'status' => 'pending',
        ]);

        // 12. Seed Company Settings
        $companySettings = [
            ['key' => 'company_name', 'value' => 'Acme HR Corporation', 'group' => 'general', 'description' => 'Corporate Company Legal Name'],
            ['key' => 'company_email', 'value' => 'hr@acme-corp.com', 'group' => 'general', 'description' => 'Main HR contact email Address'],
            ['key' => 'company_phone', 'value' => '+91-120-4455667', 'group' => 'general', 'description' => 'HQ Desk Phone Number'],
            ['key' => 'company_address', 'value' => 'Plot 4, Block B, Tech Hub Complex, Noida, UP, India', 'group' => 'general', 'description' => 'Registered Headquarters Address'],
        ];

        foreach ($companySettings as $setting) {
            CompanySetting::create($setting);
        }

        // 13. Seed Payroll Logs (for July 2026)
        foreach ($employees as $employee) {
            $structure = SalaryStructure::where('employee_id', $employee->id)->first();
            $totalAllowance = $structure->hra + $structure->medical_allowance + $structure->transport_allowance + $structure->special_allowance;
            $totalDeduction = $structure->tax_deduction + $structure->provident_fund + $structure->other_deductions;

            Payroll::create([
                'employee_id' => $employee->id,
                'payroll_code' => 'PAY-202607-'.$employee->employee_code.'-'.strtoupper(uniqid()),
                'month' => 7,
                'year' => 2026,
                'basic_salary' => $structure->basic_salary,
                'total_allowance' => $totalAllowance,
                'total_deduction' => $totalDeduction,
                'net_salary' => $structure->net_salary,
                'status' => 'paid',
                'payment_date' => '2026-07-31',
                'payment_method' => 'bank_transfer',
                'notes' => 'July 2026 salary disbursed successfully.',
            ]);
        }
    }
}
