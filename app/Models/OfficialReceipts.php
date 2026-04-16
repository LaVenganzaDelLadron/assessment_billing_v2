<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OfficialReceipts extends PrefixedModel
{
    protected $table = 'official_receipts';

    protected $fillable = [
        'payment_id',
        'or_number',
        'issued_by',
        'issued_at',
    ];

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payments::class, 'payment_id');
    }

    protected function idPrefix(): string
    {
        return 'ORC';
    }

    protected function casts(): array
    {
        return [
            'issued_at' => 'datetime',
        ];
    }
}
