<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Shift extends Model
{
    protected $fillable = [
        'name',
        'shift_code',
        'start_time',
        'end_time',
        'mark_late_after_minutes',
        'total_hours',
        'is_active',
    ];

    protected $casts = [
        'mark_late_after_minutes' => 'integer',
        'total_hours' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function employeeShifts(): HasMany
    {
        return $this->hasMany(EmployeeShift::class);
    }
}
