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
        'total_units',
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
        ];
    }
}
