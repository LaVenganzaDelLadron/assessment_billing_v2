<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Programs extends PrefixedModel
{
    protected $table = 'programs';

    public $timestamps = true;

    // Disable auto-incrementing since we manage IDs
    public $incrementing = true;

    protected $keyType = 'int';

    protected $fillable = [
        'custom_id',
        'external_id',
        'name',
        'code',
        'department',
        'status',
    ];

    public function students(): HasMany
    {
        return $this->hasMany(Students::class, 'program_id');
    }

    public function subjects(): BelongsToMany
    {
        return $this->belongsToMany(Subjects::class, 'program_subject', 'program_id', 'subject_id')
            ->withPivot('year_level', 'semester', 'school_year', 'status')
            ->withTimestamps();
    }

    public function feeStructures(): HasMany
    {
        return $this->hasMany(FeeStructure::class, 'program_id');
    }

    protected function idPrefix(): string
    {
        return 'PRG';
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
        return [];
    }
}
