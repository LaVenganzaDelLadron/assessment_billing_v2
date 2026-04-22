<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class ScholarshipRequest extends CrudRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $baseRules = [
            'name' => ['required', 'string', 'max:255', Rule::unique('scholarships', 'name')->ignore($this->routeId())],
            'description' => ['nullable', 'string'],
            'discount_type' => ['required', Rule::in(['percent', 'amount'])],
            'discount_value' => ['required', 'numeric', 'min:0'],
            'is_active' => ['sometimes', 'boolean'],
        ];

        if ($this->isMethod('put') || $this->isMethod('patch')) {
            return [
                'name' => ['sometimes', 'required', 'string', 'max:255', Rule::unique('scholarships', 'name')->ignore($this->routeId())],
                'description' => ['sometimes', 'nullable', 'string'],
                'discount_type' => ['sometimes', 'required', Rule::in(['percent', 'amount'])],
                'discount_value' => ['sometimes', 'required', 'numeric', 'min:0'],
                'is_active' => ['sometimes', 'boolean'],
            ];
        }

        return $baseRules;
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $discountType = $this->input('discount_type');
            $discountValue = $this->input('discount_value');

            if ($discountType === 'percent' && is_numeric($discountValue) && (float) $discountValue > 100) {
                $validator->errors()->add('discount_value', 'Percent discount may not be greater than 100.');
            }
        });
    }

    public function messages(): array
    {
        return [
            'name.required' => 'The scholarship name field is required.',
            'name.unique' => 'The scholarship name has already been taken.',
            'discount_type.required' => 'The discount type field is required.',
            'discount_type.in' => 'The discount type must be either percent or amount.',
            'discount_value.required' => 'The discount value field is required.',
            'discount_value.numeric' => 'The discount value must be a valid number.',
            'discount_value.min' => 'The discount value must be at least 0.',
        ];
    }
}
