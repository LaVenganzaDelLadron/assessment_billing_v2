<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Assessments extends PrefixedModel
{
    protected $table = 'assessments';

    protected $fillable = [
        'student_id',
        'academic_term_id',
        'semester',
        'school_year',
        'total_units',
        'tuition_fee',
        'misc_fee',
        'lab_fee',
        'other_fees',
        'total_amount',
        'discount',
        'net_amount',
        'status',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Students::class, 'student_id');
    }

    public function academicTerm(): BelongsTo
    {
        return $this->belongsTo(AcademicTerms::class, 'academic_term_id');
    }

    public function assessmentBreakdown(): HasMany
    {
        return $this->hasMany(AssessmentBreakdown::class, 'assessment_id');
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoices::class, 'assessment_id');
    }

    protected function idPrefix(): string
    {
        return 'ASM';
    }

    protected function casts(): array
    {
        return [
            'total_units' => 'decimal:2',
            'tuition_fee' => 'decimal:2',
            'misc_fee' => 'decimal:2',
            'lab_fee' => 'decimal:2',
            'other_fees' => 'decimal:2',
            'total_amount' => 'decimal:2',
            'discount' => 'decimal:2',
            'net_amount' => 'decimal:2',
        ];
    }
}
