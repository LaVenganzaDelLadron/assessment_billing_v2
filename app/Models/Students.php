<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Students extends PrefixedModel
{
    protected $table = 'students';

    public $incrementing = true;

    protected $keyType = 'int';

    protected $fillable = [
        'user_id',
        'student_no',
        'first_name',
        'middle_name',
        'last_name',
        'program_id',
        'year_level',
        'status',
    ];

    /**
     * Get the user associated with this student.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

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

    public function studentScholarships(): HasMany
    {
        return $this->hasMany(StudentScholarship::class, 'student_id');
    }

    protected function idPrefix(): string
    {
        return 'STU';
    }

    /**
     * Disable HasPrefixedId trait's ID generation
     */
    protected static function bootHasPrefixedId(): void
    {
        // Override to disable automatic ID generation
    }
}
