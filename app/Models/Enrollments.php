<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Enrollments extends PrefixedModel
{
    protected $table = 'enrollments';

    protected $fillable = [
        'student_id',
        'subject_id',
        'academic_term_id',
        'status',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Students::class, 'student_id');
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subjects::class, 'subject_id');
    }

    public function academicTerm(): BelongsTo
    {
        return $this->belongsTo(AcademicTerms::class, 'academic_term_id');
    }

    protected function idPrefix(): string
    {
        return 'ENR';
    }
}
