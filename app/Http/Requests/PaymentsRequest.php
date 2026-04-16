<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class PaymentsRequest extends CrudRequest
{
    public function rules(): array
    {
        if($this->isMethod('put') || $this->isMethod('patch')) {
            return [
                'invoice_id' => ['sometimes', 'required', 'exists:invoices,id'],
                'amount_paid' => ['sometimes', 'required', 'numeric', 'gt:0'],
                'payment_method' => ['sometimes', 'required', 'string', 'max:255', Rule::exists('payment_methods', 'name')],
                'reference_number' => ['nullable', 'string', 'max:255'],
                'paid_at' => ['sometimes', 'required', 'date'],
            ];
        }
        return [];

    }
    public function messages(): array
    {
        return [
            'invoice_id.exists' => 'The selected invoice does not exist.',
            'amount_paid.numeric' => 'The amount paid must be a number.',
            'amount_paid.gt' => 'The amount paid must be greater than 0.',
            'payment_method.required' => 'The payment method field is required.',
            'payment_method.string' => 'The payment method must be a string.',
            'payment_method.max' => 'The payment method may not be greater than 255 characters.',
            'payment_method.exists' => 'The selected payment method does not exist.',
            'reference_number.string' => 'The reference number must be a string.',
            'reference_number.max' => 'The reference number may not be greater than 255 characters.',
            'paid_at.required' => 'The paid at field is required.',
            'paid_at.date' => 'The paid at must be a valid date.',
        ];
    }
}
