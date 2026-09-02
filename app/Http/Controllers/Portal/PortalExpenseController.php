<?php

namespace App\Http\Controllers\Portal;

use App\Domain\Expense\ExpenseClaimStoreDto;
use App\Http\Controllers\Controller;
use App\Http\Services\ExpenseService;
use App\Models\Employee;
use App\Models\ExpenseClaim;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PortalExpenseController extends Controller
{
    public function __construct(private readonly ExpenseService $expenseService) {}

    public function index(): View
    {
        $user = Auth::user();
        $employee = Employee::where('user_id', $user->id)->first() ?? Employee::first();

        $claims = ExpenseClaim::where('employee_id', $employee->id)
            ->latest('id')
            ->paginate(15);

        $totalSubmitted = (float) ExpenseClaim::where('employee_id', $employee->id)->sum('amount');
        $totalReimbursed = (float) ExpenseClaim::where('employee_id', $employee->id)->where('status', ExpenseClaim::STATUS_REIMBURSED)->sum('amount');
        $pendingCount = ExpenseClaim::where('employee_id', $employee->id)->where('status', ExpenseClaim::STATUS_PENDING)->count();

        return view('portal.expenses.index', compact('claims', 'totalSubmitted', 'totalReimbursed', 'pendingCount'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|string',
            'amount' => 'required|numeric|min:1',
            'expense_date' => 'required|date',
            'description' => 'nullable|string',
            'receipt' => 'nullable|file|mimes:pdf,png,jpg,jpeg|max:5120',
        ]);

        $user = Auth::user();
        $employee = Employee::where('user_id', $user->id)->first() ?? Employee::first();

        $receiptPath = null;
        if ($request->hasFile('receipt')) {
            $receiptPath = $request->file('receipt')->store('receipts', 'public');
        }

        $dto = new ExpenseClaimStoreDto(
            employeeId: $employee->id,
            title: $request->title,
            category: $request->category,
            amount: (float) $request->amount,
            expenseDate: $request->expense_date,
            description: $request->description,
            receiptFile: $receiptPath
        );

        $this->expenseService->store($dto);

        return redirect()->back()->with('success', 'Expense claim submitted for approval.');
    }
}
