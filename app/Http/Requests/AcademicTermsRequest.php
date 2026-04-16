<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class AcademicTermsRequest extends CrudRequest
{
    public function rules(): array
    {
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
}
