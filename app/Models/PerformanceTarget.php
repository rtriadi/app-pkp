<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PerformanceTarget extends Model
{
    protected $fillable = [
        'agreement_id',
        'activity_goal',
        'performance_indicator',
        'quality_target',
        'quantity_target',
        'unit',
        'jan',
        'feb',
        'mar',
        'apr',
        'may',
        'jun',
        'jul',
        'aug',
        'sep',
        'oct',
        'nov',
        'dec',
    ];

    protected $casts = [
        'quality_target' => 'decimal:2',
        'quantity_target' => 'decimal:2',
        'jan' => 'boolean',
        'feb' => 'boolean',
        'mar' => 'boolean',
        'apr' => 'boolean',
        'may' => 'boolean',
        'jun' => 'boolean',
        'jul' => 'boolean',
        'aug' => 'boolean',
        'sep' => 'boolean',
        'oct' => 'boolean',
        'nov' => 'boolean',
        'dec' => 'boolean',
    ];

    public function performanceAgreement(): BelongsTo
    {
        return $this->belongsTo(PerformanceAgreement::class, 'agreement_id');
    }

    public function assessmentDetails(): HasMany
    {
        return $this->hasMany(AssessmentDetail::class, 'target_id');
    }

    public function getScheduledMonthsAttribute(): array
    {
        $months = [];
        $monthNames = ['jan', 'feb', 'mar', 'apr', 'may', 'jun', 'jul', 'aug', 'sep', 'oct', 'nov', 'dec'];

        foreach ($monthNames as $index => $month) {
            if ($this->$month) {
                $months[] = $index + 1;
            }
        }

        return $months;
    }
}
