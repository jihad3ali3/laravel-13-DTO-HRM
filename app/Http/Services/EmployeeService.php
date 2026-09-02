<?php

namespace App\Http\Services;

use App\Domain\Employee\EmployeeFilterDto;
use App\Domain\Employee\EmployeeStoreDto;
use App\Domain\Employee\EmployeeUpdateDto;
use App\Repositories\EmployeeRepository;
use Illuminate\Pagination\LengthAwarePaginator;

readonly class EmployeeService
{
    public function __construct(private EmployeeRepository $employeeRepository) {}

    public function getEmployees(EmployeeFilterDto $dto): LengthAwarePaginator
    {
        return $this->employeeRepository->getEmployees($dto);
    }

    public function show(int $id)
    {
        return $this->employeeRepository->show($id);
    }

    public function store(EmployeeStoreDto $dto)
    {
        return $this->employeeRepository->store($dto);
    }

    public function update(int $id, EmployeeUpdateDto $dto)
    {
        return $this->employeeRepository->update($id, $dto);
    }

    public function destroy(int $id): void
    {
        $this->employeeRepository->destroy($id);
    }
}
