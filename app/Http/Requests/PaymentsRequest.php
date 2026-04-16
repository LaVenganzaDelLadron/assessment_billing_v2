<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class PaymentsRequest extends CrudRequest
{
    public function rules(): array
    {
        return [
            'invoice_id' => ['required', 'exists:invoices,id'],
            'amount_paid' => ['required', 'numeric', 'gt:0'],
            'payment_method' => ['required', 'string', 'max:255', Rule::exists('payment_methods', 'name')],
            'reference_number' => ['nullable', 'string', 'max:255'],
            'paid_at' => ['required', 'date'],
        ];
    }
}
