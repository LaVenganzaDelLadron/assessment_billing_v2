<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class RefundsRequest extends CrudRequest
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
                'payment_id' => ['sometimes', 'required', 'exists:payments,id'],
                'amount' => ['sometimes', 'required', 'numeric', 'gt:0'],
                'reason' => ['sometimes', 'required', 'string'],
                'status' => ['sometimes', 'required', Rule::in(['pending', 'approved', 'rejected'])],
            ];
        }
        return [];
    }
    public function messages(): array
    {
        return [
            'payment_id.exists' => 'The selected payment does not exist.',
            'amount.numeric' => 'The amount must be a number.',
            'amount.gt' => 'The amount must be greater than 0.',
            'reason.required' => 'The reason field is required.',
            'status.required' => 'The status field is required.',
            'status.in' => 'The status must be one of the following: pending, approved, rejected.',
        ];
    }
}
