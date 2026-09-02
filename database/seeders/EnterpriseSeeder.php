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
use Carbon\CarbonPeriod;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class EnterpriseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Departments
        $departmentsData = [
            ['name' => 'Engineering & Technology', 'code' => 'ENG', 'description' => 'Software engineering, DevOps, and cloud infrastructure'],
            ['name' => 'Product & Design', 'code' => 'PROD', 'description' => 'Product strategy, UI/UX research, and technical design'],
            ['name' => 'Sales & Business Development', 'code' => 'SALES', 'description' => 'Enterprise client acquisition and revenue operations'],
            ['name' => 'Human Resources & Operations', 'code' => 'HR', 'description' => 'Workforce talent, employee experience, and compliance'],
        ];

        $departments = [];
        foreach ($departmentsData as $d) {
            $departments[$d['code']] = Department::updateOrCreate(
                ['code' => $d['code']],
                ['name' => $d['name'], 'description' => $d['description'], 'is_active' => true]
            );
        }

        // 2. Designations
        $designationsData = [
            ['department_id' => $departments['ENG']->id, 'name' => 'Senior Software Engineer', 'description' => 'Core backend & architecture'],
            ['department_id' => $departments['ENG']->id, 'name' => 'Frontend Developer', 'description' => 'Web application UI interfaces'],
            ['department_id' => $departments['ENG']->id, 'name' => 'DevOps & Cloud Engineer', 'description' => 'CI/CD and infrastructure reliability'],
            ['department_id' => $departments['PROD']->id, 'name' => 'Principal Product Manager', 'description' => 'Product roadmap and discovery'],
            ['department_id' => $departments['PROD']->id, 'name' => 'Lead UI/UX Designer', 'description' => 'User experience and design systems'],
            ['department_id' => $departments['SALES']->id, 'name' => 'Enterprise Account Executive', 'description' => 'Global enterprise accounts'],
            ['department_id' => $departments['SALES']->id, 'name' => 'Sales Development Rep', 'description' => 'Inbound/outbound pipeline qualification'],
            ['department_id' => $departments['HR']->id, 'name' => 'HR Operations Lead', 'description' => 'Payroll, benefits, and labor compliance'],
            ['department_id' => $departments['HR']->id, 'name' => 'Talent Acquisition Specialist', 'description' => 'Workforce recruiting and onboarding'],
            ['department_id' => $departments['HR']->id, 'name' => 'Chief People Officer', 'description' => 'Executive leadership & people strategy'],
        ];

        $designations = [];
        foreach ($designationsData as $desig) {
            $designations[$desig['name']] = Designation::updateOrCreate(
                ['department_id' => $desig['department_id'], 'name' => $desig['name']],
                ['description' => $desig['description'], 'is_active' => true]
            );
        }

        // 3. Shifts
        $shiftsData = [
            ['name' => 'General Morning Shift', 'shift_code' => 'GEN-MORN', 'start_time' => '09:00:00', 'end_time' => '18:00:00', 'mark_late_after_minutes' => 15, 'total_hours' => 9.0],
            ['name' => 'Early Morning Shift', 'shift_code' => 'EARLY-MORN', 'start_time' => '07:00:00', 'end_time' => '16:00:00', 'mark_late_after_minutes' => 15, 'total_hours' => 9.0],
            ['name' => 'Afternoon Evening Shift', 'shift_code' => 'AFT-EVE', 'start_time' => '13:00:00', 'end_time' => '22:00:00', 'mark_late_after_minutes' => 15, 'total_hours' => 9.0],
            ['name' => 'Overnight Operations Shift', 'shift_code' => 'NIGHT-OPS', 'start_time' => '22:00:00', 'end_time' => '06:00:00', 'mark_late_after_minutes' => 15, 'total_hours' => 8.0],
        ];

        $shifts = [];
        foreach ($shiftsData as $s) {
            $shifts[$s['shift_code']] = Shift::updateOrCreate(
                ['shift_code' => $s['shift_code']],
                $s
            );
        }

        // 4. Leave Types
        $leaveTypesData = [
            ['name' => 'Casual Leave', 'code' => 'CL', 'max_day_per_year' => 12, 'is_paid' => true],
            ['name' => 'Sick Leave', 'code' => 'SL', 'max_day_per_year' => 10, 'is_paid' => true],
            ['name' => 'Earned / Annual Leave', 'code' => 'EL', 'max_day_per_year' => 18, 'is_paid' => true],
            ['name' => 'Paternity / Maternity Leave', 'code' => 'PL', 'max_day_per_year' => 30, 'is_paid' => true],
            ['name' => 'Leave Without Pay', 'code' => 'LWP', 'max_day_per_year' => 60, 'is_paid' => false],
        ];

        $leaveTypes = [];
        foreach ($leaveTypesData as $lt) {
            $leaveTypes[$lt['code']] = Leave::updateOrCreate(
                ['code' => $lt['code']],
                ['name' => $lt['name'], 'max_day_per_year' => $lt['max_day_per_year'], 'is_paid' => $lt['is_paid'], 'is_active' => true]
            );
        }

        // 5. 2026 Company Holidays
        $holidaysData = [
            ['name' => 'New Year Holiday', 'holiday_date' => '2026-01-01', 'holiday_year' => 2026, 'type' => 'public', 'description' => 'International New Year celebration'],
            ['name' => 'Republic Day', 'holiday_date' => '2026-01-26', 'holiday_year' => 2026, 'type' => 'public', 'description' => 'National Republic Day'],
            ['name' => 'Holi Festival of Colors', 'holiday_date' => '2026-03-04', 'holiday_year' => 2026, 'type' => 'public', 'description' => 'Spring Festival of Colors'],
            ['name' => 'Good Friday', 'holiday_date' => '2026-04-03', 'holiday_year' => 2026, 'type' => 'public', 'description' => 'Good Friday holiday'],
            ['name' => 'Independence Day', 'holiday_date' => '2026-08-15', 'holiday_year' => 2026, 'type' => 'public', 'description' => 'National Independence Day'],
            ['name' => 'Gandhi Jayanti', 'holiday_date' => '2026-10-02', 'holiday_year' => 2026, 'type' => 'public', 'description' => 'Mahatma Gandhi birthday'],
            ['name' => 'Diwali Deepavali', 'holiday_date' => '2026-11-08', 'holiday_year' => 2026, 'type' => 'public', 'description' => 'Festival of Lights'],
            ['name' => 'Christmas Day', 'holiday_date' => '2026-12-25', 'holiday_year' => 2026, 'type' => 'public', 'description' => 'Christmas Day holiday'],
        ];

        foreach ($holidaysData as $h) {
            Holiday::updateOrCreate(
                ['name' => $h['name'], 'holiday_year' => $h['holiday_year']],
                $h
            );
        }

        // 6. Employees Directory & User Accounts (20 Realistic Profiles)
        $employeeProfiles = [
            [
                'email' => 'admin@example.com',
                'name' => 'Admin User',
                'role' => 'admin',
                'emp_code' => 'EMP-001',
                'first_name' => 'System',
                'last_name' => 'Administrator',
                'phone' => '+91-9876500001',
                'gender' => 'male',
                'dob' => '1988-04-12',
                'joining' => '2022-01-01',
                'dept' => 'HR',
                'desig' => 'Chief People Officer',
                'salary' => 150000.00,
            ],
            // HR Manager
            [
                'email' => 'hr@example.com',
                'name' => 'HR Manager',
                'role' => 'hr_manager',
                'emp_code' => 'EMP-002',
                'first_name' => 'Sunita',
                'last_name' => 'Sharma',
                'phone' => '+91-9876500002',
                'gender' => 'female',
                'dob' => '1990-08-20',
                'joining' => '2022-06-15',
                'dept' => 'HR',
                'desig' => 'HR Operations Lead',
                'salary' => 95000.00,
            ],
            // Standard Employee Demo Account
            [
                'email' => 'amit@example.com',
                'name' => 'Amit Kumar',
                'role' => 'employee',
                'emp_code' => 'EMP-003',
                'first_name' => 'Amit',
                'last_name' => 'Kumar',
                'phone' => '+91-9876500003',
                'gender' => 'male',
                'dob' => '1994-11-05',
                'joining' => '2023-02-01',
                'dept' => 'ENG',
                'desig' => 'Senior Software Engineer',
                'salary' => 85000.00,
            ],
            [
                'email' => 'employee@example.com',
                'name' => 'Rahul Verma',
                'role' => 'employee',
                'emp_code' => 'EMP-003B',
                'first_name' => 'Rahul',
                'last_name' => 'Verma',
                'phone' => '+91-9876500033',
                'gender' => 'male',
                'dob' => '1994-11-05',
                'joining' => '2023-02-01',
                'dept' => 'ENG',
                'desig' => 'Senior Software Engineer',
                'salary' => 85000.00,
            ],
            [
                'email' => 'priya.patel@example.com',
                'name' => 'Priya Patel',
                'role' => 'employee',
                'emp_code' => 'EMP-004',
                'first_name' => 'Priya',
                'last_name' => 'Patel',
                'phone' => '+91-9876500004',
                'gender' => 'female',
                'dob' => '1995-03-14',
                'joining' => '2023-03-10',
                'dept' => 'ENG',
                'desig' => 'Frontend Developer',
                'salary' => 70000.00,
            ],
            [
                'email' => 'vikram.singh@example.com',
                'name' => 'Vikram Singh',
                'role' => 'employee',
                'emp_code' => 'EMP-005',
                'first_name' => 'Vikram',
                'last_name' => 'Singh',
                'phone' => '+91-9876500005',
                'gender' => 'male',
                'dob' => '1992-09-28',
                'joining' => '2022-11-01',
                'dept' => 'ENG',
                'desig' => 'DevOps & Cloud Engineer',
                'salary' => 90000.00,
            ],
            [
                'email' => 'ananya.deshmukh@example.com',
                'name' => 'Ananya Deshmukh',
                'role' => 'employee',
                'emp_code' => 'EMP-006',
                'first_name' => 'Ananya',
                'last_name' => 'Deshmukh',
                'phone' => '+91-9876500006',
                'gender' => 'female',
                'dob' => '1996-07-22',
                'joining' => '2023-07-15',
                'dept' => 'ENG',
                'desig' => 'Senior Software Engineer',
                'salary' => 80000.00,
            ],
            [
                'email' => 'karthik.iyer@example.com',
                'name' => 'Karthik Iyer',
                'role' => 'employee',
                'emp_code' => 'EMP-007',
                'first_name' => 'Karthik',
                'last_name' => 'Iyer',
                'phone' => '+91-9876500007',
                'gender' => 'male',
                'dob' => '1993-01-18',
                'joining' => '2023-01-10',
                'dept' => 'ENG',
                'desig' => 'Frontend Developer',
                'salary' => 72000.00,
            ],
            [
                'email' => 'rohan.kapoor@example.com',
                'name' => 'Rohan Kapoor',
                'role' => 'employee',
                'emp_code' => 'EMP-008',
                'first_name' => 'Rohan',
                'last_name' => 'Kapoor',
                'phone' => '+91-9876500008',
                'gender' => 'male',
                'dob' => '1991-05-19',
                'joining' => '2022-09-01',
                'dept' => 'PROD',
                'desig' => 'Principal Product Manager',
                'salary' => 110000.00,
            ],
            [
                'email' => 'meera.nair@example.com',
                'name' => 'Meera Nair',
                'role' => 'employee',
                'emp_code' => 'EMP-009',
                'first_name' => 'Meera',
                'last_name' => 'Nair',
                'phone' => '+91-9876500009',
                'gender' => 'female',
                'dob' => '1994-12-03',
                'joining' => '2023-05-20',
                'dept' => 'PROD',
                'desig' => 'Lead UI/UX Designer',
                'salary' => 88000.00,
            ],
            [
                'email' => 'arjun.reddy@example.com',
                'name' => 'Arjun Reddy',
                'role' => 'employee',
                'emp_code' => 'EMP-010',
                'first_name' => 'Arjun',
                'last_name' => 'Reddy',
                'phone' => '+91-9876500010',
                'gender' => 'male',
                'dob' => '1995-10-10',
                'joining' => '2023-08-01',
                'dept' => 'PROD',
                'desig' => 'Lead UI/UX Designer',
                'salary' => 78000.00,
            ],
            [
                'email' => 'deepak.gupta@example.com',
                'name' => 'Deepak Gupta',
                'role' => 'employee',
                'emp_code' => 'EMP-011',
                'first_name' => 'Deepak',
                'last_name' => 'Gupta',
                'phone' => '+91-9876500011',
                'gender' => 'male',
                'dob' => '1989-06-30',
                'joining' => '2022-04-15',
                'dept' => 'SALES',
                'desig' => 'Enterprise Account Executive',
                'salary' => 95000.00,
            ],
            [
                'email' => 'tanvi.agarwal@example.com',
                'name' => 'Tanvi Agarwal',
                'role' => 'employee',
                'emp_code' => 'EMP-012',
                'first_name' => 'Tanvi',
                'last_name' => 'Agarwal',
                'phone' => '+91-9876500012',
                'gender' => 'female',
                'dob' => '1996-02-14',
                'joining' => '2023-09-01',
                'dept' => 'SALES',
                'desig' => 'Sales Development Rep',
                'salary' => 55000.00,
            ],
            [
                'email' => 'manish.joshi@example.com',
                'name' => 'Manish Joshi',
                'role' => 'employee',
                'emp_code' => 'EMP-013',
                'first_name' => 'Manish',
                'last_name' => 'Joshi',
                'phone' => '+91-9876500013',
                'gender' => 'male',
                'dob' => '1993-04-05',
                'joining' => '2023-06-15',
                'dept' => 'SALES',
                'desig' => 'Sales Development Rep',
                'salary' => 58000.00,
            ],
            [
                'email' => 'kavita.sen@example.com',
                'name' => 'Kavita Sen',
                'role' => 'hr_manager',
                'emp_code' => 'EMP-014',
                'first_name' => 'Kavita',
                'last_name' => 'Sen',
                'phone' => '+91-9876500014',
                'gender' => 'female',
                'dob' => '1992-11-25',
                'joining' => '2023-01-15',
                'dept' => 'HR',
                'desig' => 'Talent Acquisition Specialist',
                'salary' => 65000.00,
            ],
            [
                'email' => 'amit.bose@example.com',
                'name' => 'Amit Bose',
                'role' => 'employee',
                'emp_code' => 'EMP-015',
                'first_name' => 'Amit',
                'last_name' => 'Bose',
                'phone' => '+91-9876500015',
                'gender' => 'male',
                'dob' => '1994-08-11',
                'joining' => '2023-10-01',
                'dept' => 'ENG',
                'desig' => 'Senior Software Engineer',
                'salary' => 82000.00,
            ],
            [
                'email' => 'pooja.shukla@example.com',
                'name' => 'Pooja Shukla',
                'role' => 'employee',
                'emp_code' => 'EMP-016',
                'first_name' => 'Pooja',
                'last_name' => 'Shukla',
                'phone' => '+91-9876500016',
                'gender' => 'female',
                'dob' => '1997-01-19',
                'joining' => '2024-01-10',
                'dept' => 'ENG',
                'desig' => 'Frontend Developer',
                'salary' => 62000.00,
            ],
            [
                'email' => 'neeraj.pandey@example.com',
                'name' => 'Neeraj Pandey',
                'role' => 'employee',
                'emp_code' => 'EMP-017',
                'first_name' => 'Neeraj',
                'last_name' => 'Pandey',
                'phone' => '+91-9876500017',
                'gender' => 'male',
                'dob' => '1991-03-27',
                'joining' => '2023-04-01',
                'dept' => 'SALES',
                'desig' => 'Enterprise Account Executive',
                'salary' => 92000.00,
            ],
            [
                'email' => 'shweta.tiwari@example.com',
                'name' => 'Shweta Tiwari',
                'role' => 'employee',
                'emp_code' => 'EMP-018',
                'first_name' => 'Shweta',
                'last_name' => 'Tiwari',
                'phone' => '+91-9876500018',
                'gender' => 'female',
                'dob' => '1995-12-14',
                'joining' => '2024-02-01',
                'dept' => 'HR',
                'desig' => 'Talent Acquisition Specialist',
                'salary' => 60000.00,
            ],
            [
                'email' => 'siddharth.roy@example.com',
                'name' => 'Siddharth Roy',
                'role' => 'employee',
                'emp_code' => 'EMP-019',
                'first_name' => 'Siddharth',
                'last_name' => 'Roy',
                'phone' => '+91-9876500019',
                'gender' => 'male',
                'dob' => '1993-07-08',
                'joining' => '2023-11-15',
                'dept' => 'PROD',
                'desig' => 'Lead UI/UX Designer',
                'salary' => 75000.00,
            ],
            [
                'email' => 'harsh.vardhan@example.com',
                'name' => 'Harsh Vardhan',
                'role' => 'employee',
                'emp_code' => 'EMP-020',
                'first_name' => 'Harsh',
                'last_name' => 'Vardhan',
                'phone' => '+91-9876500020',
                'gender' => 'male',
                'dob' => '1990-10-31',
                'joining' => '2022-08-01',
                'dept' => 'ENG',
                'desig' => 'DevOps & Cloud Engineer',
                'salary' => 96000.00,
            ],
        ];

        $createdEmployees = [];
        foreach ($employeeProfiles as $p) {
            $user = User::updateOrCreate(
                ['email' => $p['email']],
                [
                    'name' => $p['name'],
                    'password' => Hash::make('password123'),
                ]
            );

            // Assign role
            $user->syncRoles([$p['role']]);

            $deptId = $departments[$p['dept']]->id;
            $desigId = $designations[$p['desig']]->id;

            $employee = Employee::updateOrCreate(
                ['employee_code' => $p['emp_code']],
                [
                    'user_id' => $user->id,
                    'first_name' => $p['first_name'],
                    'last_name' => $p['last_name'],
                    'email' => $p['email'],
                    'phone' => $p['phone'],
                    'gender' => $p['gender'],
                    'date_of_birth' => $p['dob'],
                    'joining_date' => $p['joining'],
                    'department_id' => $deptId,
                    'designation_id' => $desigId,
                    'employment_type' => 'full_time',
                    'salary' => $p['salary'],
                    'is_active' => true,
                ]
            );

            $createdEmployees[] = $employee;

            // Assign Shift (Morning Shift)
            EmployeeShift::updateOrCreate(
                ['employee_id' => $employee->id],
                [
                    'shift_id' => $shifts['GEN-MORN']->id,
                    'start_date' => '2026-01-01',
                    'end_date' => '2026-12-31',
                    'is_active' => true,
                ]
            );

            // Configure Salary Structure
            $basic = round($p['salary'] * 0.50, 2);
            $hra = round($basic * 0.40, 2);
            $medical = 2500.00;
            $conveyance = 3000.00;
            $special = max(0, $p['salary'] - ($basic + $hra + $medical + $conveyance));
            $pf = round($basic * 0.12, 2);
            $ptax = 200.00;

            SalaryStructure::updateOrCreate(
                ['employee_id' => $employee->id],
                [
                    'basic_salary' => $basic,
                    'hra' => $hra,
                    'medical_allowance' => $medical,
                    'transport_allowance' => $conveyance,
                    'special_allowance' => $special,
                    'provident_fund' => $pf,
                    'tax_deduction' => $ptax,
                    'other_deductions' => 0.00,
                    'net_salary' => round(($basic + $hra + $medical + $conveyance + $special) - ($pf + $ptax), 2),
                ]
            );
        }

        // 7. Historical Attendance (Last 30 Days)
        $today = Carbon::now();
        $startDate = Carbon::now()->subDays(29);
        $period = CarbonPeriod::create($startDate, $today);

        foreach ($createdEmployees as $idx => $emp) {
            foreach ($period as $dt) {
                if ($dt->isWeekend()) {
                    continue;
                }

                $dateStr = $dt->toDateString();
                $rand = ($idx + $dt->day) % 10;

                if ($rand == 0) {
                    $status = Attendance::STATUS_LATE;
                    $checkIn = '09:25:00';
                    $checkOut = '18:15:00';
                    $hours = 8.8;
                } elseif ($rand == 1) {
                    $status = Attendance::STATUS_HALF_DAY;
                    $checkIn = '09:00:00';
                    $checkOut = '13:30:00';
                    $hours = 4.5;
                } elseif ($rand == 2 && $idx > 10) {
                    $status = Attendance::STATUS_ABSENT;
                    $checkIn = '00:00:00';
                    $checkOut = '00:00:00';
                    $hours = 0.0;
                } else {
                    $status = Attendance::STATUS_PRESENT;
                    $checkIn = '08:55:00';
                    $checkOut = '18:05:00';
                    $hours = 9.1;
                }

                Attendance::updateOrCreate(
                    ['employee_id' => $emp->id, 'attendance_date' => $dateStr],
                    [
                        'check_in' => $checkIn,
                        'check_out' => $checkOut,
                        'status' => $status,
                        'working_hours' => $hours,
                    ]
                );
            }
        }

        // 8. Leave Requests
        $sampleLeaves = [
            ['employee_id' => $createdEmployees[2]->id, 'leave_id' => $leaveTypes['CL']->id, 'start_date' => '2026-08-10', 'end_date' => '2026-08-11', 'reason' => 'Personal work at home.', 'status' => 'approved'],
            ['employee_id' => $createdEmployees[3]->id, 'leave_id' => $leaveTypes['SL']->id, 'start_date' => '2026-08-20', 'end_date' => '2026-08-21', 'reason' => 'Viral flu and doctor checkup.', 'status' => 'approved'],
            ['employee_id' => $createdEmployees[4]->id, 'leave_id' => $leaveTypes['EL']->id, 'start_date' => '2026-09-15', 'end_date' => '2026-09-18', 'reason' => 'Family vacation trip.', 'status' => 'pending'],
            ['employee_id' => $createdEmployees[5]->id, 'leave_id' => $leaveTypes['CL']->id, 'start_date' => '2026-09-08', 'end_date' => '2026-09-08', 'reason' => 'Bank documentation work.', 'status' => 'pending'],
            ['employee_id' => $createdEmployees[6]->id, 'leave_id' => $leaveTypes['SL']->id, 'start_date' => '2026-07-05', 'end_date' => '2026-07-06', 'reason' => 'Dental surgery recovery.', 'status' => 'approved'],
            ['employee_id' => $createdEmployees[7]->id, 'leave_id' => $leaveTypes['CL']->id, 'start_date' => '2026-07-28', 'end_date' => '2026-07-29', 'reason' => 'Emergency travel.', 'status' => 'rejected'],
        ];

        foreach ($sampleLeaves as $l) {
            LeaveRequest::updateOrCreate(
                ['employee_id' => $l['employee_id'], 'start_date' => $l['start_date']],
                $l
            );
        }

        // 9. Payroll Batches (June, July, August 2026)
        $months = [6, 7, 8];
        foreach ($months as $m) {
            foreach ($createdEmployees as $emp) {
                $basic = round($emp->salary * 0.50, 2);
                $allowance = round($emp->salary * 0.50, 2);
                $deduction = round($basic * 0.12 + 200, 2);
                $net = round($emp->salary - $deduction, 2);

                Payroll::updateOrCreate(
                    ['employee_id' => $emp->id, 'month' => $m, 'year' => 2026],
                    [
                        'payroll_code' => "PAY-20260{$m}-{$emp->employee_code}",
                        'basic_salary' => $basic,
                        'total_allowance' => $allowance,
                        'total_deduction' => $deduction,
                        'net_salary' => $net,
                        'status' => 'paid',
                        'payment_date' => "2026-0{$m}-28",
                        'payment_method' => 'bank_transfer',
                    ]
                );
            }
        }

        // 10. Expense Claims
        $sampleExpenses = [
            ['employee_id' => $createdEmployees[2]->id, 'claim_code' => 'CLM-2026-001', 'title' => 'Client Lunch Meeting', 'category' => 'meals', 'amount' => 2450.00, 'expense_date' => '2026-08-15', 'description' => 'Business lunch with prospective enterprise clients.', 'status' => 'reimbursed'],
            ['employee_id' => $createdEmployees[4]->id, 'claim_code' => 'CLM-2026-002', 'title' => 'AWS Certified Architect Exam Fee', 'category' => 'training', 'amount' => 12500.00, 'expense_date' => '2026-08-18', 'description' => 'Cloud certification reimbursement approved by manager.', 'status' => 'accepted'],
            ['employee_id' => $createdEmployees[8]->id, 'claim_code' => 'CLM-2026-003', 'title' => 'Ergonomic Mouse & Keyboard', 'category' => 'equipment', 'amount' => 4500.00, 'expense_date' => '2026-08-25', 'description' => 'Office equipment for design workstation.', 'status' => 'pending'],
            ['employee_id' => $createdEmployees[10]->id, 'claim_code' => 'CLM-2026-004', 'title' => 'Cab Fare to Conference', 'category' => 'travel', 'amount' => 1800.00, 'expense_date' => '2026-08-28', 'description' => 'Travel reimbursement for sales pitch in Gurgaon.', 'status' => 'pending'],
        ];

        foreach ($sampleExpenses as $ex) {
            ExpenseClaim::updateOrCreate(
                ['claim_code' => $ex['claim_code']],
                $ex
            );
        }

        // 11. Performance Goals & Reviews
        foreach ($createdEmployees as $idx => $emp) {
            PerformanceGoal::updateOrCreate(
                ['employee_id' => $emp->id, 'title' => "Core Objective Q3 - {$emp->first_name}"],
                [
                    'description' => 'Deliver target quarterly sprint milestones and optimize system performance metrics.',
                    'start_date' => '2026-07-01',
                    'due_date' => '2026-09-30',
                    'progress' => ($idx % 4) * 25 + 25,
                    'status' => ($idx % 4 == 3) ? 'completed' : 'in_progress',
                ]
            );

            PerformanceReview::updateOrCreate(
                ['employee_id' => $emp->id, 'review_cycle' => 'Q2'],
                [
                    'reviewer_id' => $createdEmployees[0]->user_id,
                    'rating' => 4,
                    'strengths' => 'Technical depth, accountability, and problem-solving velocity.',
                    'area_of_improvements' => 'Can take broader ownership of junior developer mentoring.',
                    'reviewed_at' => '2026-07-05',
                ]
            );
        }

        // 12. Announcements
        $announcementsData = [
            ['title' => 'Q3 All-Hands Townhall & Product Strategy', 'content' => 'Join our executive leadership team this Thursday at 4 PM for the quarterly townhall showcasing upcoming product releases and celebrating team milestones.', 'priority' => 'high', 'start_date' => '2026-09-01', 'end_date' => '2026-09-10', 'is_pinned' => true, 'is_published' => true],
            ['title' => 'Updated Group Health Insurance Benefits 2026', 'content' => 'The revised group medical insurance policy document has been uploaded to the portal. Coverage now includes wellness checkups and outpatient allowances.', 'priority' => 'medium', 'start_date' => '2026-08-25', 'end_date' => '2026-09-30', 'is_pinned' => true, 'is_published' => true],
            ['title' => 'Annual Hackathon & Innovation Sprint', 'content' => 'Registrations are now open for our annual 48-hour hackathon happening next month. Prizes include innovation grants and tech gadgets!', 'priority' => 'low', 'start_date' => '2026-09-02', 'end_date' => '2026-09-25', 'is_pinned' => false, 'is_published' => true],
        ];

        foreach ($announcementsData as $an) {
            Announcement::updateOrCreate(
                ['title' => $an['title']],
                $an
            );
        }

        // 13. System Notifications
        foreach ($createdEmployees as $idx => $emp) {
            Notification::updateOrCreate(
                ['employee_id' => $emp->id, 'title' => 'August Salary Disbursed'],
                [
                    'type' => 'payroll_disbursed',
                    'message' => 'Your monthly salary for August 2026 has been deposited into your registered bank account.',
                    'channel' => 'database',
                    'read_at' => $idx % 2 == 0 ? now() : null,
                ]
            );
        }

        // 14. Company Settings
        $settings = [
            'company_name' => 'HRM Enterprise Inc.',
            'company_email' => 'hr@hrm-enterprise.com',
            'company_phone' => '+91-11-45678900',
            'company_address' => 'Cyber City Tech Park, Tower 4, Phase 2, Gurugram, India',
            'website' => 'https://hrm-enterprise.com',
            'currency' => 'INR',
            'currency_symbol' => '₹',
            'timezone' => 'Asia/Kolkata',
            'date_format' => 'd M, Y',
            'tax_number' => '07AAAAA9999Z1Z8',
            'working_hours_per_day' => '8.5',
            'working_days_per_week' => '5',
        ];

        foreach ($settings as $k => $v) {
            CompanySetting::updateOrCreate(
                ['key' => $k],
                ['value' => $v, 'group' => 'general']
            );
        }
    }
}
