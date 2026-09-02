<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payroll extends Model
{
    const STATUS_DRAFT = 'draft';

    const STATUS_GENERATED = 'generated';

    const STATUS_PAID = 'paid';

    protected $fillable = [
        'employee_id',
        'payroll_code',
        'month',
        'year',
        'basic_salary',
        'total_allowance',
        'net_salary',
        'total_deduction',
        'status',
        'payment_date',
        'payment_method',
        'notes',
    ];

    protected $casts = [
        'month' => 'integer',
        'year' => 'integer',
        'basic_salary' => 'decimal:2',
        'total_allowance' => 'decimal:2',
        'net_salary' => 'decimal:2',
        'total_deduction' => 'decimal:2',
        'payment_date' => 'date',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}
