<?php

namespace App\Http\Controllers\Admin\Payroll;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Payroll\PayrollGenerateRequest;
use App\Http\Requests\Api\V1\Payroll\SalaryStructureUpdateRequest;
use App\Http\Services\PayrollService;
use App\Models\Employee;
use App\Models\Payroll;
use App\Models\SalaryStructure;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PayrollController extends Controller
{
    public function __construct(private readonly PayrollService $payrollService) {}

    public function index(Request $request): View
    {
        $selectedMonth = (int) $request->input('month', Carbon::now()->month);
        $selectedYear = (int) $request->input('year', Carbon::now()->year);

        $query = Payroll::with(['employee.department', 'employee.designation'])
            ->where('month', $selectedMonth)
            ->where('year', $selectedYear);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $payrolls = $query->latest('id')->paginate(15);

        $totalDisbursed = (float) (clone $query)->where('status', 'paid')->sum('net_salary');
        $totalPending = (float) (clone $query)->where('status', 'draft')->sum('net_salary');
        $paidCount = (clone $query)->where('status', 'paid')->count();
        $draftCount = (clone $query)->where('status', 'draft')->count();

        return view('admin.payroll.index', compact(
            'payrolls',
            'selectedMonth',
            'selectedYear',
            'totalDisbursed',
            'totalPending',
            'paidCount',
            'draftCount'
        ));
    }

    public function generate(PayrollGenerateRequest $request): RedirectResponse
    {
        $result = $this->payrollService->generate($request->toDto());
        $count = count($result);

        return redirect()->route('admin::payroll.index', [
            'month' => $request->month,
            'year' => $request->year,
        ])->with('success', "Payroll batch generated successfully for {$count} active employees.");
    }

    public function markAsPaid(int $id): RedirectResponse
    {
        $this->payrollService->markAsPaid($id);

        return redirect()->back()->with('success', 'Payroll record marked as Paid.');
    }

    public function payslip(int $id): View
    {
        $payroll = Payroll::with(['employee.department', 'employee.designation'])->findOrFail($id);

        return view('admin.payroll.payslip', compact('payroll'));
    }

    public function structures(): View
    {
        $structures = SalaryStructure::with(['employee.department', 'employee.designation'])->paginate(15);
        $employees = Employee::active()->orderBy('first_name')->get();

        return view('admin.payroll.structures', compact('structures', 'employees'));
    }

    public function updateStructure(SalaryStructureUpdateRequest $request): RedirectResponse
    {
        $this->payrollService->updateSalaryStructure($request->toDto());

        return redirect()->back()->with('success', 'Salary structure saved successfully.');
    }
}
