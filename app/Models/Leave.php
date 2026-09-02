<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Leave extends Model
{
    protected $fillable = [
        'name',
        'code',
        'max_day_per_year',
        'is_paid',
        'is_active',
    ];

    protected $casts = [
        'is_paid' => 'boolean',
        'is_active' => 'boolean',
        'max_day_per_year' => 'integer',
    ];

    public function leaveRequests(): HasMany
    {
        return $this->hasMany(LeaveRequest::class);
    }

    public function getDaysAllowedAttribute(): int
    {
        return (int) ($this->attributes['max_day_per_year'] ?? 12);
    }
}
