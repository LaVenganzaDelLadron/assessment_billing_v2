<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class StudentsRequest extends CrudRequest
{
    public function rules(): array
    {
        return [
            'student_no' => ['required', 'string', 'max:255', Rule::unique('students', 'student_no')->ignore($this->routeId())],
            'first_name' => ['required', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('students', 'email')->ignore($this->routeId())],
            'program_id' => ['required', 'exists:programs,id'],
            'year_level' => ['required', 'integer', 'min:1', 'max:255'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ];
    }
}
