<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class PaymentAllocationsRequest extends CrudRequest
{
    public function rules(): array
    {
        return [
            'payment_id' => ['required', 'exists:payments,id'],
            'invoice_id' => [
                'required',
                'exists:invoices,id',
                Rule::unique('payment_allocations', 'invoice_id')
                    ->where(fn ($query) => $query->where('payment_id', $this->string('payment_id')->toString()))
                    ->ignore($this->routeId()),
            ],
            'amount_applied' => ['required', 'numeric', 'gt:0'],
        ];
    }
}
