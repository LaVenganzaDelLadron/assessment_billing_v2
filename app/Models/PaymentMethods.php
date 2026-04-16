<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;

class PaymentMethods extends PrefixedModel
{
    protected $table = 'payment_methods';

    protected $fillable = [
        'name',
    ];

    public function payments(): HasMany
    {
        return $this->hasMany(Payments::class, 'payment_method', 'name');
    }

    protected function idPrefix(): string
    {
        return 'PMD';
    }
}
