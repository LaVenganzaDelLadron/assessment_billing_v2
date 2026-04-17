<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class StudentsRequest extends CrudRequest
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
                'student_no' => ['sometimes', 'required', 'string', 'max:255', Rule::unique('students', 'student_no')->ignore($this->routeId())],
                'first_name' => ['sometimes', 'required', 'string', 'max:255'],
                'middle_name' => ['sometimes', 'nullable', 'string', 'max:255'],
                'last_name' => ['sometimes', 'required', 'string', 'max:255'],
                'email' => ['sometimes', 'required', 'email', 'max:255', Rule::unique('students', 'email')->ignore($this->routeId())],
                'program_id' => ['sometimes', 'required', 'exists:programs,id'],
                'year_level' => ['sometimes', 'required', 'integer', 'min:1', 'max:255'],
                'status' => ['sometimes', 'required', Rule::in(['active', 'inactive'])],
            ];
        }
        return [];
    }
    public function messages(): array
    {
        return [
            'student_no.required' => 'The student number field is required.',
            'student_no.unique' => 'The student number has already been taken.',
            'email.required' => 'The email field is required.',
            'email.email' => 'The email must be a valid email address.',
            'email.unique' => 'The email has already been taken.',
            'first_name.required' => 'The first name field is required.',
            'last_name.required' => 'The last name field is required.',
            'program_id.required' => 'The program field is required.',
            'year_level.required' => 'The year level field is required.',
            'year_level.integer' => 'The year level must be an integer.',
            'year_level.min' => 'The year level must be at least 1.',
            'year_level.max' => 'The year level may not be greater than 255.',
            'status.required' => 'The status field is required.',
            'status.in' => 'The status must be one of the following: active, inactive.',
        ];
    }
}
