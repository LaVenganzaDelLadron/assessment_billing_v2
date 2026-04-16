<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class EnrollmentsRequest extends CrudRequest
{
    public function rules(): array
    {
        if($this->isMethod('put') || $this->isMethod('patch')) {
            return [
                'student_id' => ['sometimes', 'required', 'exists:students,id'],
                'subject_id' => ['sometimes', 'required', 'exists:subjects,id'],
                'academic_term_id' => [
                    'sometimes',
                    'required',
                    'exists:academic_terms,id',
                    Rule::unique('enrollments', 'academic_term_id')
                        ->where(fn ($query) => $query
                            ->where('student_id', $this->string('student_id')->toString())
                            ->where('subject_id', $this->string('subject_id')->toString()))
                        ->ignore($this->routeId()),
                ],
                'semester' => ['sometimes', 'required', 'string', 'max:20'],
                'school_year' => ['sometimes', 'required', 'string', 'max:9'],
                'status' => ['sometimes', 'required', Rule::in(['enrolled', 'dropped'])],
            ];
        }
        return [];
    }
    public function messages(): array
    {
        return [
            'student_id.exists' => 'The selected student does not exist.',
            'subject_id.exists' => 'The selected subject does not exist.',
            'academic_term_id.unique' => 'The combination of student, subject, and academic term has already been taken.',
            'semester.required' => 'The semester field is required.',
            'school_year.required' => 'The school year field is required.',
            'status.required' => 'The status field is required.',
            'status.in' => "The status must be either 'enrolled' or 'dropped'.",
        ];
    }
}
