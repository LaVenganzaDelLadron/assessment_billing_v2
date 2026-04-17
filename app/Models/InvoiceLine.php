<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvoiceLine extends Model
{
    protected $table = 'invoice_lines';

    protected $fillable = [
        'invoice_id',
        'line_type',
        'subject_id',
        'description',
        'quantity',
        'unit_price',
        'amount',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'amount' => 'decimal:2',
    ];

    /**
     * Get the invoice this line belongs to.
     */
    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoices::class, 'invoice_id');
    }

    /**
     * Get the subject (if this is a tuition line).
     */
    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subjects::class, 'subject_id');
    }

    /**
     * Calculate the total for this line.
     */
    public function calculateAmount(): decimal
    {
        if ($this->quantity) {
            return $this->quantity * $this->unit_price;
        }

        return $this->unit_price;
    }
}
