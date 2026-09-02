<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeDocument extends Model
{
    protected $table = 'employee_documents';

    protected $fillable = [
        'employee_id',
        'title',
        'document_type',
        'file_name',
        'file_path',
        'expiry_date',
        'verification_status',
        'verified_at',
        'notes',
    ];

    protected $casts = [
        'expiry_date' => 'date',
        'verified_at' => 'datetime',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}
