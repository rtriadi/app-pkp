<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MonthlyAssessment extends Model
{
    protected $fillable = [
        'agreement_id',
        'month',
        'year',
        'status',
        'total_score',
        'grade',
        'assessor_id',
        'supervisor_id',
        'notes',
    ];

    protected $casts = [
        'month' => 'integer',
        'year' => 'integer',
        'total_score' => 'decimal:2',
    ];

    public function performanceAgreement(): BelongsTo
    {
        return $this->belongsTo(PerformanceAgreement::class, 'agreement_id');
    }

    public function assessor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assessor_id');
    }

    public function supervisor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }

    public function assessmentDetails(): HasMany
    {
        return $this->hasMany(AssessmentDetail::class, 'assessment_id');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class, 'assessment_id');
    }

    public function calculateGrade(): string
    {
        $score = $this->total_score;

        if ($score >= 91) return 'Sangat Baik';
        if ($score >= 76) return 'Baik';
        if ($score >= 61) return 'Cukup';
        if ($score >= 51) return 'Kurang';
        return 'Sangat Kurang';
    }

    protected static function booted()
    {
        static::saving(function ($assessment) {
            if ($assessment->total_score) {
                $assessment->grade = $assessment->calculateGrade();
            }
        });
    }
}
