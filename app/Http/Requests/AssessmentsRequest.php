<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class AssessmentsRequest extends CrudRequest
{
    public function rules(): array
    {
        return [
            'student_id' => ['required', 'exists:students,id'],
            'academic_term_id' => ['required', 'exists:academic_terms,id'],
            'semester' => ['required', 'string', 'max:20'],
            'school_year' => ['required', 'string', 'max:9'],
            'total_units' => ['required', 'numeric', 'min:0'],
            'tuition_fee' => ['required', 'numeric', 'min:0'],
            'misc_fee' => ['required', 'numeric', 'min:0'],
            'lab_fee' => ['required', 'numeric', 'min:0'],
            'other_fees' => ['required', 'numeric', 'min:0'],
            'total_amount' => ['required', 'numeric', 'min:0'],
            'discount' => ['required', 'numeric', 'min:0'],
            'net_amount' => ['required', 'numeric', 'min:0'],
            'status' => ['required', Rule::in(['draft', 'finalized'])],
        ];
    }
}
