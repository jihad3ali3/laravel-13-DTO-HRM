<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalaryStructure extends Model
{
    protected $fillable = [
        'employee_id',
        'basic_salary',
        'hra',
        'medical_allowance',
        'transport_allowance',
        'special_allowance',
        'tax_deduction',
        'provident_fund',
        'other_deductions',
        'net_salary',
    ];

    protected $casts = [
        'basic_salary' => 'decimal:2',
        'hra' => 'decimal:2',
        'medical_allowance' => 'decimal:2',
        'transport_allowance' => 'decimal:2',
        'special_allowance' => 'decimal:2',
        'tax_deduction' => 'decimal:2',
        'provident_fund' => 'decimal:2',
        'other_deductions' => 'decimal:2',
        'net_salary' => 'decimal:2',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}
