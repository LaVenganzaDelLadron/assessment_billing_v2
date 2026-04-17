<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class AssessmentBreakdownRequest extends CrudRequest
{
    public function authorize(): bool
    {
        return true;
    }


    public function rules(): array
    {
        if($this->isMethod('put') || $this->isMethod('patch')) {
            return [
                'assessment_id' => ['sometimes', 'required', 'exists:assessments,id'],
                'source_type' => ['sometimes', 'required', Rule::in(['subject', 'fee', 'discount'])],
                'source_id' => [
                    Rule::requiredIf(fn () => in_array($this->input('source_type'), ['subject', 'fee'], true)),
                    'nullable',
                    'string',
                    'max:255',
                ],
                'description' => ['sometimes', 'required', 'string', 'max:255'],
                'units' => ['sometimes', 'nullable', 'numeric', 'min:0'],
                'rate' => ['sometimes', 'nullable', 'numeric', 'min:0'],
                'amount' => ['sometimes', 'required', 'numeric'],
            ];
        }
        return [];
    }
    public function messages(): array
    {
        return [
            'assessment_id.required' => 'The assessment ID field is required.',
            'assessment_id.exists' => 'The selected assessment does not exist.',
            'source_type.required' => 'The source type field is required.',
            'source_type.in' => 'The source type must be one of the following: subject, fee, discount.',
            'source_id.required_if' => 'The source ID field is required when the source type is subject or fee.',
            'source_id.string' => 'The source ID must be a string.',
            'source_id.max' => 'The source ID may not be greater than 255 characters.',
            'description.required' => 'The description field is required.',
            'description.string' => 'The description must be a string.',
            'description.max' => 'The description may not be greater than 255 characters.',
            'units.numeric' => 'The units must be a number.',
            'units.min' => 'The units must be at least 0.',
            'rate.numeric' => 'The rate must be a number.',
            'rate.min' => 'The rate must be at least 0.',
            'amount.required' => 'The amount field is required.',
            'amount.numeric' => 'The amount must be a number.',
        ];
    }
}
