<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PerformanceReview extends Model
{
    protected $fillable = [
        'employee_id',
        'reviewer_id',
        'review_cycle',
        'reviewed_at',
        'rating',
        'strengths',
        'area_of_improvements',
        'employee_feedback',
        'status',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
        'rating' => 'integer',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    public function getReviewPeriodAttribute(): string
    {
        return $this->attributes['review_cycle'] ?? 'Annual';
    }

    public function getFeedbackAttribute(): ?string
    {
        return $this->attributes['strengths'] ?? $this->attributes['area_of_improvements'] ?? null;
    }

    public function getAreasForImprovementAttribute(): ?string
    {
        return $this->attributes['area_of_improvements'] ?? null;
    }

    public function getReviewDateAttribute(): ?string
    {
        return $this->reviewed_at ? $this->reviewed_at->toDateString() : null;
    }
}
