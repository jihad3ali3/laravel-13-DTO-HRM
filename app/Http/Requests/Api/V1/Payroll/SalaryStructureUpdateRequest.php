<?php

namespace App\Http\Requests\Api\V1\Payroll;

use App\Domain\Payroll\SalaryStructureUpdateDto;
use Illuminate\Foundation\Http\FormRequest;

class SalaryStructureUpdateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'employee_id' => 'required|exists:employees,id',
            'basic_salary' => 'required|numeric|min:0',
            'hra' => 'required|numeric|min:0',
            'medical_allowance' => 'required|numeric|min:0',
            'transport_allowance' => 'required|numeric|min:0',
            'special_allowance' => 'required|numeric|min:0',
            'tax_deduction' => 'required|numeric|min:0',
            'provident_fund' => 'required|numeric|min:0',
            'other_deductions' => 'required|numeric|min:0',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }

    public function toDto(): SalaryStructureUpdateDto
    {
        return new SalaryStructureUpdateDto(
            employeeId: (int) $this->input('employee_id'),
            basicSalary: (float) $this->input('basic_salary'),
            hra: (float) $this->input('hra'),
            medicalAllowance: (float) $this->input('medical_allowance'),
            transportAllowance: (float) $this->input('transport_allowance'),
            specialAllowance: (float) $this->input('special_allowance'),
            taxDeduction: (float) $this->input('tax_deduction'),
            providentFund: (float) $this->input('provident_fund'),
            otherDeductions: (float) $this->input('other_deductions')
        );
    }
}
