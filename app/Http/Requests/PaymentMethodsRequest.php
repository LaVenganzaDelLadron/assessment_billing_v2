<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class PaymentMethodsRequest extends CrudRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', Rule::unique('payment_methods', 'name')->ignore($this->routeId())],
        ];
    }
}
