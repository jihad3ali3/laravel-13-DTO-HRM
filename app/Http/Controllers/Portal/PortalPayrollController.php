<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Payroll;
use App\Models\SalaryStructure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PortalPayrollController extends Controller
{
    public function index(Request $request): View
    {
        $user = Auth::user();
        $employee = Employee::where('user_id', $user->id)->first() ?? Employee::first();

        $payrolls = Payroll::where('employee_id', $employee->id)
            ->latest('id')
            ->paginate(15);

        $structure = SalaryStructure::where('employee_id', $employee->id)->first();
        $totalEarned = (float) Payroll::where('employee_id', $employee->id)->where('status', 'paid')->sum('net_salary');

        return view('portal.payroll.index', compact('employee', 'payrolls', 'structure', 'totalEarned'));
    }

    public function payslip(int $id): View
    {
        $user = Auth::user();
        $employee = Employee::where('user_id', $user->id)->first() ?? Employee::first();

        $payroll = Payroll::with(['employee.department', 'employee.designation'])
            ->where('employee_id', $employee->id)
            ->findOrFail($id);

        return view('admin.payroll.payslip', compact('payroll'));
    }
}
