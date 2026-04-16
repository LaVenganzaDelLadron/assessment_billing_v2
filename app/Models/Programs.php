<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;

class Programs extends PrefixedModel
{
    protected $table = 'programs';

    protected $fillable = [
        'name',
        'department',
        'tuition_per_unit',
    ];

    public function students(): HasMany
    {
        return $this->hasMany(Students::class, 'program_id');
    }

    public function subjects(): HasMany
    {
        return $this->hasMany(Subjects::class, 'program_id');
    }

    public function feeStructures(): HasMany
    {
        return $this->hasMany(FeeStructure::class, 'program_id');
    }

    protected function idPrefix(): string
    {
        return 'PRG';
    }

    protected function casts(): array
    {
        return [
            'tuition_per_unit' => 'decimal:2',
        ];
    }
}
