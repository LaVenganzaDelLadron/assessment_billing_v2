<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class AssessmentBreakdownRequest extends CrudRequest
{
    public function rules(): array
    {
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
}
