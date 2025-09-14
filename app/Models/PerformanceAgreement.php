<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PerformanceAgreement extends Model
{
    protected $fillable = [
        'employee_id',
        'year',
        'status',
        'approved_by',
        'approved_at',
        'created_by',
    ];

    protected $casts = [
        'year' => 'integer',
        'approved_at' => 'datetime',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function performanceTargets(): HasMany
    {
        return $this->hasMany(PerformanceTarget::class, 'agreement_id');
    }

    public function monthlyAssessments(): HasMany
    {
        return $this->hasMany(MonthlyAssessment::class, 'agreement_id');
    }
}
