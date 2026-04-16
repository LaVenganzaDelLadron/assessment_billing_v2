<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Payments extends PrefixedModel
{
    protected $table = 'payments';

    protected $fillable = [
        'invoice_id',
        'amount_paid',
        'payment_method',
        'reference_number',
        'paid_at',
    ];

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoices::class, 'invoice_id');
    }

    public function paymentAllocations(): HasMany
    {
        return $this->hasMany(PaymentAllocations::class, 'payment_id');
    }

    public function officialReceipt(): HasOne
    {
        return $this->hasOne(OfficialReceipts::class, 'payment_id');
    }

    public function refunds(): HasMany
    {
        return $this->hasMany(Refunds::class, 'payment_id');
    }

    protected function idPrefix(): string
    {
        return 'PAY';
    }

    protected function casts(): array
    {
        return [
            'amount_paid' => 'decimal:2',
            'paid_at' => 'datetime',
        ];
    }
}
