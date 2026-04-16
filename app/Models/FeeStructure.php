<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FeeStructure extends PrefixedModel
{
    protected $table = 'fee_structure';

    protected $fillable = [
        'program_id',
        'fee_type',
        'amount',
        'per_unit',
    ];

    public function program(): BelongsTo
    {
        return $this->belongsTo(Programs::class, 'program_id');
    }

    protected function idPrefix(): string
    {
        return 'FEE';
    }

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'per_unit' => 'boolean',
        ];
    }
}
