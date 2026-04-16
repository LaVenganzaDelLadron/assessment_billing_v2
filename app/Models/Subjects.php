<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subjects extends PrefixedModel
{
    protected $table = 'subjects';

    protected $fillable = [
        'code',
        'name',
        'units',
        'program_id',
    ];

    public function program(): BelongsTo
    {
        return $this->belongsTo(Programs::class, 'program_id');
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollments::class, 'subject_id');
    }

    protected function idPrefix(): string
    {
        return 'SUB';
    }

    protected function casts(): array
    {
        return [
            'units' => 'decimal:2',
        ];
    }
}
