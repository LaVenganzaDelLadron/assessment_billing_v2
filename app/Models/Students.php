<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Students extends PrefixedModel
{
    protected $table = 'students';

    protected $fillable = [
        'student_no',
        'first_name',
        'middle_name',
        'last_name',
        'email',
        'program_id',
        'year_level',
        'status',
    ];

    public function program(): BelongsTo
    {
        return $this->belongsTo(Programs::class, 'program_id');
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollments::class, 'student_id');
    }

    public function assessments(): HasMany
    {
        return $this->hasMany(Assessments::class, 'student_id');
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoices::class, 'student_id');
    }

    protected function idPrefix(): string
    {
        return 'STU';
    }
}
