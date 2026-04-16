<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;

class AcademicTerms extends PrefixedModel
{
    protected $table = 'academic_terms';

    protected $fillable = [
        'school_year',
        'semester',
        'start_date',
        'end_date',
        'is_active',
    ];

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollments::class, 'academic_term_id');
    }

    public function assessments(): HasMany
    {
        return $this->hasMany(Assessments::class, 'academic_term_id');
    }

    protected function idPrefix(): string
    {
        return 'ACT';
    }

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'is_active' => 'boolean',
        ];
    }
}
