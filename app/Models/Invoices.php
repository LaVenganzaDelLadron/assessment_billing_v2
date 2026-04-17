<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoices extends PrefixedModel
{
    protected $table = 'invoices';

    protected $fillable = [
        'student_id',
        'assessment_id',
        'invoice_number',
        'total_amount',
        'balance',
        'due_date',
        'status',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Students::class, 'student_id');
    }

    public function assessment(): BelongsTo
    {
        return $this->belongsTo(Assessments::class, 'assessment_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payments::class, 'invoice_id');
    }

    public function paymentAllocations(): HasMany
    {
        return $this->hasMany(PaymentAllocations::class, 'invoice_id');
    }

    public function invoiceLines(): HasMany
    {
        return $this->hasMany(InvoiceLine::class, 'invoice_id');
    }

    protected function idPrefix(): string
    {
        return 'INV';
    }

    protected function casts(): array
    {
        return [
            'total_amount' => 'decimal:2',
            'balance' => 'decimal:2',
            'due_date' => 'date',
        ];
    }
}
