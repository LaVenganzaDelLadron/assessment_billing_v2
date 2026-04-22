<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class ApplyScholarshipRequest extends CrudRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'student_id' => ['required', 'exists:students,id'],
            'scholarship_id' => ['required', 'exists:scholarships,id'],
            'original_amount' => ['required', 'numeric', 'min:0'],
            'applied_at' => ['nullable', 'date'],
            'discount_type' => ['nullable', Rule::in(['percent', 'amount'])],
            'discount_value' => ['nullable', 'numeric', 'min:0'],
        ];
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
            'student_id.required' => 'The student field is required.',
            'student_id.exists' => 'The selected student does not exist.',
            'scholarship_id.required' => 'The scholarship field is required.',
            'scholarship_id.exists' => 'The selected scholarship does not exist.',
            'original_amount.required' => 'The original amount field is required.',
            'original_amount.numeric' => 'The original amount must be a valid number.',
            'original_amount.min' => 'The original amount must be at least 0.',
            'discount_type.in' => 'The discount type must be either percent or amount.',
            'discount_value.numeric' => 'The discount value must be a valid number.',
            'discount_value.min' => 'The discount value must be at least 0.',
        ];
    }
}
