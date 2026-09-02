<?php

namespace App\Http\Services;

use App\Domain\Leave\LeaveFilterDto;
use App\Domain\Leave\LeaveRequestProcessDto;
use App\Domain\Leave\LeaveRequestStoreDto;
use App\Repositories\LeaveRepository;
use Illuminate\Pagination\LengthAwarePaginator;

readonly class LeaveService
{
    public function __construct(private LeaveRepository $leaveRepository) {}

    public function getTypes()
    {
        return $this->leaveRepository->getTypes();
    }

    public function getRequests(LeaveFilterDto $dto): LengthAwarePaginator
    {
        return $this->leaveRepository->getRequests($dto);
    }

    public function store(LeaveRequestStoreDto $dto)
    {
        return $this->leaveRepository->store($dto);
    }

    public function process(int $id, LeaveRequestProcessDto $dto)
    {
        return $this->leaveRepository->process($id, $dto);
    }

    public function getBalances(int $employeeId): array
    {
        return $this->leaveRepository->getBalances($employeeId);
    }
}
