<?php

namespace App\Repositories;

use App\Domain\Expense\ExpenseClaimFilterDto;
use App\Domain\Expense\ExpenseClaimProcessDto;
use App\Domain\Expense\ExpenseClaimStoreDto;
use App\Exceptions\RepositoryException;
use App\Models\ExpenseClaim;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class ExpenseRepository
{
    public function getClaims(ExpenseClaimFilterDto $dto): LengthAwarePaginator
    {
        return ExpenseClaim::query()
            ->with(['employee:id,first_name,last_name,employee_code'])
            ->when($dto->employeeId, fn ($q, $id) => $q->where('employee_id', $id))
            ->when($dto->status, fn ($q, $status) => $q->where('status', $status))
            ->latest()
            ->paginate($dto->perPage);
    }

    /**
     * @throws RepositoryException
     */
    public function show(int $id): ExpenseClaim
    {
        $claim = ExpenseClaim::with(['employee'])->find($id);
        if (! $claim) {
            throw new RepositoryException('Expense claim not found', ResponseAlias::HTTP_NOT_FOUND);
        }

        return $claim;
    }

    public function store(ExpenseClaimStoreDto $dto): ExpenseClaim
    {
        $claimCode = 'EXP-'.strtoupper(uniqid());

        return ExpenseClaim::create([
            'employee_id' => $dto->employeeId,
            'claim_code' => $claimCode,
            'title' => $dto->title,
            'category' => $dto->category,
            'amount' => $dto->amount,
            'expense_date' => $dto->expenseDate,
            'description' => $dto->description,
            'receipt_file' => $dto->receiptFile,
            'status' => ExpenseClaim::STATUS_PENDING,
        ]);
    }

    /**
     * @throws RepositoryException
     */
    public function process(int $id, ExpenseClaimProcessDto $dto): ExpenseClaim
    {
        $claim = ExpenseClaim::find($id);
        if (! $claim) {
            throw new RepositoryException('Expense claim not found', ResponseAlias::HTTP_NOT_FOUND);
        }

        $updateData = [
            'status' => $dto->status,
            'actioned_by' => $dto->actionedBy,
            'actioned_at' => Carbon::now(),
        ];

        if ($dto->status === 'rejected') {
            $updateData['reject_reason'] = $dto->rejectReason;
        } elseif ($dto->status === 'reimbursed') {
            $updateData['reimbursed_date'] = Carbon::now()->toDateString();
        }

        $claim->update($updateData);

        return $claim;
    }
}
