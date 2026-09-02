<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeaveRequest extends Model
{
    const STATUS_PENDING = 'pending';

    const STATUS_APPROVED = 'approved';

    const STATUS_REJECTED = 'rejected';

    protected $fillable = [
        'employee_id',
        'leave_id',
        'start_date',
        'end_date',
        'status',
        'reason',
        'actioned_by',
        'actioned_at',
        'reject_reason',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'actioned_at' => 'datetime',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function leave(): BelongsTo
    {
        return $this->belongsTo(Leave::class);
    }

    public function actionedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actioned_by');
    }
}
