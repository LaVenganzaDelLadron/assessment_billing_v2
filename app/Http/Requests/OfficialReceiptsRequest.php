<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class OfficialReceiptsRequest extends CrudRequest
{
    public function rules(): array
    {
        return [
            'payment_id' => ['required', 'exists:payments,id', Rule::unique('official_receipts', 'payment_id')->ignore($this->routeId())],
            'or_number' => ['required', 'string', 'max:255', Rule::unique('official_receipts', 'or_number')->ignore($this->routeId())],
            'issued_by' => ['required', 'string', 'max:255'],
            'issued_at' => ['required', 'date'],
        ];
    }
}
