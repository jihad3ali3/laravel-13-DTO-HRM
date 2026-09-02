<?php

namespace App\Http\Controllers\Admin\Export;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Employee;
use App\Models\ExpenseClaim;
use App\Models\LeaveRequest;
use App\Models\Payroll;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportController extends Controller
{
    /**
     * Export Employee Directory to CSV
     */
    public function employeesCsv(Request $request): StreamedResponse
    {
        $fileName = 'hrm_employees_'.date('Y-m-d_His').'.csv';

        $query = Employee::with(['department', 'designation']);

        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }
        if ($request->filled('employment_type')) {
            $query->where('employment_type', $request->employment_type);
        }

        $employees = $query->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        return response()->stream(function () use ($employees) {
            $handle = fopen('php://output', 'w');

            // CSV Header Row
            fputcsv($handle, [
                'Employee Code',
                'First Name',
                'Last Name',
                'Email',
                'Phone',
                'Gender',
                'Department',
                'Designation',
                'Employment Type',
                'Monthly Salary',
                'Joining Date',
                'Status',
            ]);

            foreach ($employees as $emp) {
                fputcsv($handle, [
                    $emp->employee_code,
                    $emp->first_name,
                    $emp->last_name ?? '',
                    $emp->email,
                    $emp->phone ?? '',
                    ucfirst($emp->gender),
                    $emp->department->name ?? 'N/A',
                    $emp->designation->name ?? 'N/A',
                    ucfirst(str_replace('_', ' ', $emp->employment_type)),
                    number_format((float) $emp->salary, 2, '.', ''),
                    $emp->joining_date ? Carbon::parse($emp->joining_date)->format('Y-m-d') : '',
                    $emp->is_active ? 'Active' : 'Inactive',
                ]);
            }

            fclose($handle);
        }, 200, $headers);
    }

    /**
     * Printable Employee Master Roll for audits
     */
    public function employeesPrint(Request $request): View
    {
        $employees = Employee::with(['department', 'designation', 'salaryStructure'])
            ->orderBy('department_id')
            ->get();

        return view('admin.export.employee-roll-print', compact('employees'));
    }

    /**
     * Export Attendance Timesheets to CSV
     */
    public function attendanceCsv(Request $request): StreamedResponse
    {
        $fileName = 'hrm_attendance_'.date('Y-m-d_His').'.csv';

        $query = Attendance::with(['employee.department', 'employee.designation']);

        if ($request->filled('month')) {
            $query->whereMonth('attendance_date', $request->month);
        }
        if ($request->filled('year')) {
            $query->whereYear('attendance_date', $request->year);
        }
        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        $attendances = $query->orderBy('attendance_date', 'desc')->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        return response()->stream(function () use ($attendances) {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, [
                'Date',
                'Employee Code',
                'Employee Name',
                'Department',
                'Check In',
                'Check Out',
                'Working Hours',
                'Status',
                'Notes',
            ]);

            foreach ($attendances as $att) {
                fputcsv($handle, [
                    $att->attendance_date,
                    $att->employee->employee_code ?? '',
                    ($att->employee->first_name ?? '').' '.($att->employee->last_name ?? ''),
                    $att->employee->department->name ?? 'N/A',
                    $att->check_in ?? '--',
                    $att->check_out ?? '--',
                    $att->working_hours ?? '0.0',
                    ucfirst($att->status),
                    $att->notes ?? '',
                ]);
            }

            fclose($handle);
        }, 200, $headers);
    }

    /**
     * Export Payroll Disbursements to CSV
     */
    public function payrollCsv(Request $request): StreamedResponse
    {
        $fileName = 'hrm_payroll_'.date('Y-m-d_His').'.csv';

        $query = Payroll::with(['employee.department', 'employee.designation']);

        if ($request->filled('month')) {
            $query->where('month', $request->month);
        }
        if ($request->filled('year')) {
            $query->where('year', $request->year);
        }

        $payrolls = $query->latest('id')->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        return response()->stream(function () use ($payrolls) {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, [
                'Payroll Code',
                'Period (Month/Year)',
                'Employee Code',
                'Employee Name',
                'Department',
                'Basic Salary',
                'Total Allowances',
                'Total Deductions',
                'Net Salary',
                'Payment Status',
                'Payment Date',
                'Payment Method',
            ]);

            foreach ($payrolls as $p) {
                fputcsv($handle, [
                    $p->payroll_code,
                    "{$p->month}/{$p->year}",
                    $p->employee->employee_code ?? '',
                    ($p->employee->first_name ?? '').' '.($p->employee->last_name ?? ''),
                    $p->employee->department->name ?? 'N/A',
                    number_format((float) $p->basic_salary, 2, '.', ''),
                    number_format((float) $p->total_allowance, 2, '.', ''),
                    number_format((float) $p->total_deduction, 2, '.', ''),
                    number_format((float) $p->net_salary, 2, '.', ''),
                    ucfirst($p->status),
                    $p->payment_date ?? '--',
                    ucfirst(str_replace('_', ' ', $p->payment_method ?? 'bank_transfer')),
                ]);
            }

            fclose($handle);
        }, 200, $headers);
    }

    /**
     * Printable Payroll Statement
     */
    public function payrollPrint(Request $request): View
    {
        $month = (int) $request->input('month', Carbon::now()->month);
        $year = (int) $request->input('year', Carbon::now()->year);

        $payrolls = Payroll::with(['employee.department', 'employee.designation'])
            ->where('month', $month)
            ->where('year', $year)
            ->get();

        $totalDisbursed = (float) $payrolls->where('status', 'paid')->sum('net_salary');
        $totalDeductions = (float) $payrolls->sum('total_deduction');

        return view('admin.export.payroll-sheet-print', compact('payrolls', 'month', 'year', 'totalDisbursed', 'totalDeductions'));
    }

    /**
     * Export Leave Requests to CSV
     */
    public function leavesCsv(Request $request): StreamedResponse
    {
        $fileName = 'hrm_leaves_'.date('Y-m-d_His').'.csv';

        $leaves = LeaveRequest::with(['employee.department', 'leave'])
            ->latest('id')
            ->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        return response()->stream(function () use ($leaves) {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, [
                'ID',
                'Employee Code',
                'Employee Name',
                'Department',
                'Leave Type',
                'Start Date',
                'End Date',
                'Duration (Days)',
                'Status',
                'Reason',
            ]);

            foreach ($leaves as $l) {
                $start = Carbon::parse($l->start_date);
                $end = Carbon::parse($l->end_date);
                $days = $start->diffInDays($end) + 1;

                fputcsv($handle, [
                    $l->id,
                    $l->employee->employee_code ?? '',
                    ($l->employee->first_name ?? '').' '.($l->employee->last_name ?? ''),
                    $l->employee->department->name ?? 'N/A',
                    $l->leave->name ?? 'N/A',
                    $l->start_date,
                    $l->end_date,
                    $days,
                    ucfirst($l->status),
                    $l->reason ?? '',
                ]);
            }

            fclose($handle);
        }, 200, $headers);
    }

    /**
     * Export Expense Claims to CSV
     */
    public function expensesCsv(Request $request): StreamedResponse
    {
        $fileName = 'hrm_expenses_'.date('Y-m-d_His').'.csv';

        $claims = ExpenseClaim::with(['employee.department'])
            ->latest('id')
            ->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        return response()->stream(function () use ($claims) {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, [
                'Claim Code',
                'Title',
                'Employee Code',
                'Employee Name',
                'Department',
                'Category',
                'Amount (INR)',
                'Expense Date',
                'Status',
                'Description',
            ]);

            foreach ($claims as $c) {
                fputcsv($handle, [
                    $c->claim_code,
                    $c->title,
                    $c->employee->employee_code ?? '',
                    ($c->employee->first_name ?? '').' '.($c->employee->last_name ?? ''),
                    $c->employee->department->name ?? 'N/A',
                    ucfirst($c->category),
                    number_format((float) $c->amount, 2, '.', ''),
                    $c->expense_date,
                    ucfirst($c->status),
                    $c->description ?? '',
                ]);
            }

            fclose($handle);
        }, 200, $headers);
    }
}
