<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExpenseClaim extends Model
{
    protected $table = 'expenses_claims';

    const STATUS_PENDING = 'pending';

    const STATUS_ACCEPTED = 'accepted';

    const STATUS_REJECTED = 'rejected';

    const STATUS_REIMBURSED = 'reimbursed';

    protected $fillable = [
        'employee_id',
        'claim_code',
        'title',
        'category',
        'amount',
        'expense_date',
        'description',
        'receipt_file',
        'status',
        'actioned_by',
        'actioned_at',
        'reject_reason',
        'reimbursed_date',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'expense_date' => 'date',
        'actioned_at' => 'datetime',
        'reimbursed_date' => 'date',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function actionedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actioned_by');
    }
}
