<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PerformanceGoal extends Model
{
    protected $fillable = [
        'employee_id',
        'title',
        'description',
        'start_date',
        'due_date',
        'progress',
        'priority',
        'status',
    ];

    protected $casts = [
        'start_date' => 'date',
        'due_date' => 'date',
        'progress' => 'integer',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function getEndDateAttribute(): ?string
    {
        return $this->due_date ? $this->due_date->toDateString() : null;
    }
}
