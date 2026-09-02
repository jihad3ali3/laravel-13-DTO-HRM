<?php

namespace App\Http\Services;

use App\Domain\Expense\ExpenseClaimFilterDto;
use App\Domain\Expense\ExpenseClaimProcessDto;
use App\Domain\Expense\ExpenseClaimStoreDto;
use App\Repositories\ExpenseRepository;
use Illuminate\Pagination\LengthAwarePaginator;

readonly class ExpenseService
{
    public function __construct(private ExpenseRepository $expenseRepository) {}

    public function getClaims(ExpenseClaimFilterDto $dto): LengthAwarePaginator
    {
        return $this->expenseRepository->getClaims($dto);
    }

    public function show(int $id)
    {
        return $this->expenseRepository->show($id);
    }

    public function store(ExpenseClaimStoreDto $dto)
    {
        return $this->expenseRepository->store($dto);
    }

    public function process(int $id, ExpenseClaimProcessDto $dto)
    {
        return $this->expenseRepository->process($id, $dto);
    }
}
