<?php

namespace App\Repositories;

use App\Domain\Employee\EmployeeFilterDto;
use App\Domain\Employee\EmployeeStoreDto;
use App\Domain\Employee\EmployeeUpdateDto;
use App\Exceptions\RepositoryException;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class EmployeeRepository
{
    public function getEmployees(EmployeeFilterDto $dto): LengthAwarePaginator
    {
        return Employee::query()
            ->with(['department', 'designation', 'user'])
            ->when($dto->departmentId, fn ($q, $id) => $q->where('department_id', $id))
            ->when($dto->designationId, fn ($q, $id) => $q->where('designation_id', $id))
            ->when($dto->search, function ($q, $search) {
                $q->where(function ($sub) use ($search) {
                    $sub->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('employee_code', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate($dto->perPage);
    }

    /**
     * @throws RepositoryException
     */
    public function show(int $id): Employee
    {
        $employee = Employee::with(['department', 'designation', 'user'])->find($id);
        if (! $employee) {
            throw new RepositoryException('Employee not found', ResponseAlias::HTTP_NOT_FOUND);
        }

        return $employee;
    }

    public function store(EmployeeStoreDto $dto): Employee
    {
        return DB::transaction(function () use ($dto) {
            $user = User::create([
                'name' => trim($dto->firstName.' '.$dto->lastName),
                'email' => $dto->email,
                'password' => Hash::make($dto->password),
            ]);

            $user->assignRole($dto->role);

            return Employee::create([
                'user_id' => $user->id,
                'employee_code' => $dto->employeeCode,
                'first_name' => $dto->firstName,
                'last_name' => $dto->lastName,
                'email' => $dto->email,
                'phone' => $dto->phone,
                'gender' => $dto->gender,
                'date_of_birth' => $dto->dateOfBirth,
                'joining_date' => $dto->joiningDate,
                'department_id' => $dto->departmentId,
                'designation_id' => $dto->designationId,
                'employment_type' => $dto->employmentType,
                'is_active' => true,
                'salary' => $dto->salary,
                'avatar' => $dto->avatar,
                'address' => $dto->address,
                'emergency_contact' => $dto->emergencyContact,
            ]);
        });
    }

    /**
     * @throws RepositoryException
     */
    public function update(int $id, EmployeeUpdateDto $dto): Employee
    {
        return DB::transaction(function () use ($id, $dto) {
            $employee = Employee::find($id);
            if (! $employee) {
                throw new RepositoryException('Employee not found', ResponseAlias::HTTP_NOT_FOUND);
            }

            $employee->update([
                'first_name' => $dto->firstName,
                'last_name' => $dto->lastName,
                'phone' => $dto->phone,
                'gender' => $dto->gender,
                'date_of_birth' => $dto->dateOfBirth,
                'department_id' => $dto->departmentId,
                'designation_id' => $dto->designationId,
                'employment_type' => $dto->employmentType,
                'salary' => $dto->salary,
                'address' => $dto->address,
                'emergency_contact' => $dto->emergencyContact,
                'is_active' => $dto->isActive,
            ]);

            if ($employee->user) {
                $employee->user->update([
                    'name' => trim($dto->firstName.' '.$dto->lastName),
                ]);
            }

            return $employee;
        });
    }

    /**
     * @throws RepositoryException
     */
    public function destroy(int $id): void
    {
        DB::transaction(function () use ($id) {
            $employee = Employee::find($id);
            if (! $employee) {
                throw new RepositoryException('Employee not found', ResponseAlias::HTTP_NOT_FOUND);
            }

            $user = $employee->user;
            $employee->delete();
            if ($user) {
                $user->delete();
            }
        });
    }
}
