<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;

class Scholarship extends PrefixedModel
{
    protected $table = 'scholarships';

    protected $fillable = [
        'name',
        'description',
        'discount_type',
        'discount_value',
        'is_active',
    ];

    protected $casts = [
        'discount_value' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function studentScholarships(): HasMany
    {
        return $this->hasMany(StudentScholarship::class, 'scholarship_id');
    }

    protected function idPrefix(): string
    {
        return 'SCH';
    }

    protected static function bootHasPrefixedId(): void
    {
        // Override to disable automatic ID generation.
    }
}
