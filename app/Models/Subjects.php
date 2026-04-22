<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subjects extends PrefixedModel
{
    protected $table = 'subjects';

    public $timestamps = true;

    // Disable auto-incrementing since we manage IDs
    public $incrementing = true;

    protected $keyType = 'int';

    protected $fillable = [
        'custom_id',
        'external_id',
        'code',
        'subject_code',
        'name',
        'units',
        'type',
        'status',
        'program_id',
    ];

    public function program(): BelongsTo
    {
        return $this->belongsTo(Programs::class, 'program_id');
    }

    public function programs(): BelongsToMany
    {
        return $this->belongsToMany(Programs::class, 'program_subject', 'subject_id', 'program_id')
            ->withPivot('year_level', 'semester', 'school_year', 'status')
            ->withTimestamps();
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollments::class, 'subject_id');
    }

    public function teacherAssignments(): HasMany
    {
        return $this->hasMany(SubjectTeacherAssignment::class, 'subject_id');
    }

    protected function idPrefix(): string
    {
        return 'SUB';
    }

    /**
     * Disable HasPrefixedId trait's ID generation
     * We manage IDs manually for data sync
     */
    protected static function bootHasPrefixedId(): void
    {
        // Override to disable automatic ID generation
    }

    protected function casts(): array
    {
        return [
            'units' => 'decimal:2',
        ];
    }
}
