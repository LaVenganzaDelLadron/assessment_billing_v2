<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Refunds extends PrefixedModel
{
    protected $table = 'refunds';

    protected $fillable = [
        'payment_id',
        'amount',
        'reason',
        'status',
    ];

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payments::class, 'payment_id');
    }

    protected function idPrefix(): string
    {
        return 'REF';
    }

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
        ];
    }
}
