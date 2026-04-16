<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class RefundsRequest extends CrudRequest
{
    public function rules(): array
    {
        return [
            'payment_id' => ['required', 'exists:payments,id'],
            'amount' => ['required', 'numeric', 'gt:0'],
            'reason' => ['required', 'string'],
            'status' => ['required', Rule::in(['pending', 'approved', 'rejected'])],
        ];
    }
}
