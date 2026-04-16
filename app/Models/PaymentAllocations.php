<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentAllocations extends PrefixedModel
{
    protected $table = 'payment_allocations';

    protected $fillable = [
        'payment_id',
        'invoice_id',
        'amount_applied',
    ];

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payments::class, 'payment_id');
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoices::class, 'invoice_id');
    }

    protected function idPrefix(): string
    {
        return 'PAL';
    }

    protected function casts(): array
    {
        return [
            'amount_applied' => 'decimal:2',
        ];
    }
}
