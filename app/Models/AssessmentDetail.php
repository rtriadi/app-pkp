<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssessmentDetail extends Model
{
    protected $fillable = [
        'assessment_id',
        'target_id',
        'ak_target',
        'quantity_realization',
        'quality_realization',
        'score',
    ];

    protected $casts = [
        'ak_target' => 'decimal:2',
        'quantity_realization' => 'decimal:2',
        'quality_realization' => 'decimal:2',
        'score' => 'decimal:2',
    ];

    public function monthlyAssessment(): BelongsTo
    {
        return $this->belongsTo(MonthlyAssessment::class, 'assessment_id');
    }

    public function performanceTarget(): BelongsTo
    {
        return $this->belongsTo(PerformanceTarget::class, 'target_id');
    }

    protected static function booted()
    {
        static::saving(function ($detail) {
            // Calculate score: (Target + Realization) / 2
            if ($detail->ak_target && $detail->quantity_realization && $detail->quality_realization) {
                $target_avg = ($detail->ak_target + $detail->quantity_realization) / 2;
                $realization_avg = ($detail->quality_realization + $detail->quantity_realization) / 2;
                $detail->score = ($target_avg + $realization_avg) / 2;
            }
        });
    }
}
