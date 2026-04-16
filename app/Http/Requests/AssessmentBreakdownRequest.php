<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class AssessmentBreakdownRequest extends CrudRequest
{
    public function rules(): array
    {
        return [
            'assessment_id' => ['required', 'exists:assessments,id'],
            'source_type' => ['required', Rule::in(['subject', 'fee', 'discount'])],
            'source_id' => [
                Rule::requiredIf(fn () => in_array($this->input('source_type'), ['subject', 'fee'], true)),
                'nullable',
                'string',
                'max:255',
            ],
            'description' => ['required', 'string', 'max:255'],
            'units' => ['nullable', 'numeric', 'min:0'],
            'rate' => ['nullable', 'numeric', 'min:0'],
            'amount' => ['required', 'numeric'],
        ];
    }
}
