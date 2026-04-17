<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class OfficialReceiptsRequest extends CrudRequest
{

    public function authorize(): bool
    {
        return true;
    }
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        if($this->isMethod('put') || $this->isMethod('patch')) {
            return [
                'payment_id' => ['sometimes', 'required', 'exists:payments,id', Rule::unique('official_receipts', 'payment_id')->ignore($this->routeId())],
                'or_number' => ['sometimes', 'required', 'string', 'max:255', Rule::unique('official_receipts', 'or_number')->ignore($this->routeId())],
                'issued_by' => ['sometimes', 'required', 'string', 'max:255'],
                'issued_at' => ['sometimes', 'required', 'date'],
            ];
        }
        return [];
    }
    public function messages(): array
    {
        return [
            'payment_id.exists' => 'The selected payment does not exist.',
            'payment_id.unique' => 'The payment has already been associated with an official receipt.',
            'or_number.unique' => 'The official receipt number has already been taken.',
            'issued_by.required' => 'The issued by field is required.',
            'issued_at.required' => 'The issued at field is required.',
            'issued_at.date' => 'The issued at must be a valid date.',
        ];
    }
}
