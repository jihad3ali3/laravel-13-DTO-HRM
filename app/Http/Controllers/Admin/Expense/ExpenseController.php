<?php

namespace App\Http\Controllers\Admin\Expense;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Expense\ExpenseClaimFilterRequest;
use App\Http\Requests\Api\V1\Expense\ExpenseClaimProcessRequest;
use App\Http\Requests\Api\V1\Expense\ExpenseClaimStoreRequest;
use App\Http\Services\ExpenseService;
use App\Models\Employee;
use App\Models\ExpenseClaim;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ExpenseController extends Controller
{
    public function __construct(private readonly ExpenseService $expenseService) {}

    public function index(ExpenseClaimFilterRequest $request): View
    {
        $claims = $this->expenseService->getClaims($request->toDto());
        $employees = Employee::active()->orderBy('first_name')->get();

        $pendingCount = ExpenseClaim::where('status', ExpenseClaim::STATUS_PENDING)->count();
        $acceptedCount = ExpenseClaim::where('status', ExpenseClaim::STATUS_ACCEPTED)->count();
        $reimbursedCount = ExpenseClaim::where('status', ExpenseClaim::STATUS_REIMBURSED)->count();
        $totalReimbursed = (float) ExpenseClaim::where('status', ExpenseClaim::STATUS_REIMBURSED)->sum('amount');

        return view('admin.expenses.index', compact(
            'claims',
            'employees',
            'pendingCount',
            'acceptedCount',
            'reimbursedCount',
            'totalReimbursed'
        ));
    }

    public function store(ExpenseClaimStoreRequest $request): RedirectResponse
    {
        $this->expenseService->store($request->toDto());

        return redirect()->back()->with('success', 'Expense claim submitted successfully.');
    }

    public function process(int $id, ExpenseClaimProcessRequest $request): RedirectResponse
    {
        $this->expenseService->process($id, $request->toDto());

        return redirect()->back()->with('success', 'Expense claim status updated to '.ucfirst($request->status).'.');
    }
}
