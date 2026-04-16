<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class EnrollmentsRequest extends CrudRequest
{
    public function rules(): array
    {
        return [
            'student_id' => ['required', 'exists:students,id'],
            'subject_id' => ['required', 'exists:subjects,id'],
            'academic_term_id' => [
                'required',
                'exists:academic_terms,id',
                Rule::unique('enrollments', 'academic_term_id')
                    ->where(fn ($query) => $query
                        ->where('student_id', $this->string('student_id')->toString())
                        ->where('subject_id', $this->string('subject_id')->toString()))
                    ->ignore($this->routeId()),
            ],
            'semester' => ['required', 'string', 'max:20'],
            'school_year' => ['required', 'string', 'max:9'],
            'status' => ['required', Rule::in(['enrolled', 'dropped'])],
        ];
    }
}
