<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class AcademicTermsRequest extends CrudRequest
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
                'school_year' => ['sometimes', 'required', 'string', 'max:9'],
                'semester' => [
                    'sometimes',
                    'required',
                    'string',
                    'max:20',
                    Rule::unique('academic_terms', 'semester')
                        ->where(fn ($query) => $query->where('school_year', $this->string('school_year')->toString()))
                        ->ignore($this->routeId()),
                ],
                'start_date' => ['sometimes', 'required', 'date'],
                'end_date' => ['sometimes', 'required', 'date', 'after_or_equal:start_date'],
                'is_active' => ['sometimes', 'required', 'boolean'],
            ];
        }
        return [];
    }
    public function messages(): array
    {
        return [
            'school_year.required' => 'The school year field is required.',
            'school_year.string' => 'The school year must be a string.',
            'school_year.max' => 'The school year may not be greater than 9 characters.',
            'semester.required' => 'The semester field is required.',
            'semester.string' => 'The semester must be a string.',
            'semester.max' => 'The semester may not be greater than 20 characters.',
            'semester.unique' => 'The combination of school year and semester must be unique.',
            'start_date.required' => 'The start date field is required.',
            'start_date.date' => 'The start date must be a valid date.',
            'end_date.required' => 'The end date field is required.',
            'end_date.date' => 'The end date must be a valid date.',
            'end_date.after_or_equal' => 'The end date must be after or equal to the start date.',
            'is_active.required' => 'The is active field is required.',
            'is_active.boolean' => 'The is active field must be true or false.',
        ];
    }
}
