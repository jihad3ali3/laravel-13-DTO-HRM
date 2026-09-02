<?php

namespace App\Repositories;

use App\Domain\Payroll\PayrollGenerateDto;
use App\Domain\Payroll\SalaryStructureUpdateDto;
use App\Exceptions\RepositoryException;
use App\Models\Employee;
use App\Models\Payroll;
use App\Models\SalaryStructure;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class PayrollRepository
{
    public function getPayrolls()
    {
        return Payroll::with('employee:id,first_name,last_name,employee_code')->latest()->get();
    }

    /**
     * @throws RepositoryException
     */
    public function generate(PayrollGenerateDto $dto): array
    {
        $employees = Employee::active()->get();
        $generated = [];

        foreach ($employees as $employee) {
            // Check if already exists
            $exists = Payroll::where('employee_id', $employee->id)
                ->where('month', $dto->month)
                ->where('year', $dto->year)
                ->exists();

            if ($exists) {
                continue;
            }

            // Get or create salary structure
            $structure = SalaryStructure::where('employee_id', $employee->id)->first();
            if (! $structure) {
                $structure = SalaryStructure::create([
                    'employee_id' => $employee->id,
                    'basic_salary' => $employee->salary,
                    'net_salary' => $employee->salary,
                ]);
            }

            $totalAllowance = $structure->hra + $structure->medical_allowance + $structure->transport_allowance + $structure->special_allowance;
            $totalDeduction = $structure->tax_deduction + $structure->provident_fund + $structure->other_deductions;
            $netSalary = $structure->basic_salary + $totalAllowance - $totalDeduction;

            $payrollCode = 'PAY-'.$dto->year.str_pad((string) $dto->month, 2, '0', STR_PAD_LEFT).'-'.strtoupper(uniqid());

            $generated[] = Payroll::create([
                'employee_id' => $employee->id,
                'payroll_code' => $payrollCode,
                'month' => $dto->month,
                'year' => $dto->year,
                'basic_salary' => $structure->basic_salary,
                'total_allowance' => $totalAllowance,
                'total_deduction' => $totalDeduction,
                'net_salary' => $netSalary,
                'status' => Payroll::STATUS_GENERATED,
            ]);
        }

        return $generated;
    }

    /**
     * @throws RepositoryException
     */
    public function show(int $id): Payroll
    {
        $payroll = Payroll::with(['employee:id,first_name,last_name,employee_code,joining_date,department_id,designation_id', 'employee.department', 'employee.designation'])->find($id);
        if (! $payroll) {
            throw new RepositoryException('Payroll record not found', ResponseAlias::HTTP_NOT_FOUND);
        }

        return $payroll;
    }

    public function salaryStructure(int $employeeId): SalaryStructure
    {
        return SalaryStructure::firstOrCreate(
            ['employee_id' => $employeeId],
            [
                'basic_salary' => 0,
                'net_salary' => 0,
            ]
        );
    }

    public function updateSalaryStructure(SalaryStructureUpdateDto $dto): SalaryStructure
    {
        $totalAllowance = $dto->hra + $dto->medicalAllowance + $dto->transportAllowance + $dto->specialAllowance;
        $totalDeduction = $dto->taxDeduction + $dto->providentFund + $dto->otherDeductions;
        $netSalary = $dto->basicSalary + $totalAllowance - $totalDeduction;

        return SalaryStructure::updateOrCreate(
            ['employee_id' => $dto->employeeId],
            [
                'basic_salary' => $dto->basicSalary,
                'hra' => $dto->hra,
                'medical_allowance' => $dto->medicalAllowance,
                'transport_allowance' => $dto->transportAllowance,
                'special_allowance' => $dto->specialAllowance,
                'tax_deduction' => $dto->taxDeduction,
                'provident_fund' => $dto->providentFund,
                'other_deductions' => $dto->otherDeductions,
                'net_salary' => $netSalary,
            ]
        );
    }

    /**
     * @throws RepositoryException
     */
    public function markAsPaid(int $id): Payroll
    {
        $payroll = Payroll::find($id);
        if (! $payroll) {
            throw new RepositoryException('Payroll record not found', ResponseAlias::HTTP_NOT_FOUND);
        }

        $payroll->update([
            'status' => Payroll::STATUS_PAID,
            'payment_date' => Carbon::now()->toDateString(),
        ]);

        return $payroll;
    }
}
