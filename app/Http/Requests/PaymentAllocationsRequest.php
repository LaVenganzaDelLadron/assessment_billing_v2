<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class PaymentAllocationsRequest extends CrudRequest
{
    public function rules(): array
    {
        if($this->isMethod('put') || $this->isMethod('patch')) {
            return [
                'payment_id' => ['sometimes', 'required', 'exists:payments,id'],
                'invoice_id' => [
                    'sometimes',
                    'required',
                    'exists:invoices,id',
                    Rule::unique('payment_allocations', 'invoice_id')
                        ->where(fn ($query) => $query->where('payment_id', $this->string('payment_id')->toString()))
                        ->ignore($this->routeId()),
                ],
                'amount_applied' => ['sometimes', 'required', 'numeric', 'gt:0'],
            ];
        }
        return [];
    }
    public function messages(): array
    {
        return [
            'payment_id.exists' => 'The selected payment does not exist.',
            'invoice_id.exists' => 'The selected invoice does not exist.',
            'invoice_id.unique' => 'This invoice has already been allocated to the specified payment.',
            'amount_applied.numeric' => 'The amount applied must be a number.',
            'amount_applied.gt' => 'The amount applied must be greater than 0.',
        ];
    }
}
