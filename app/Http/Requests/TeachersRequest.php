<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class TeachersRequest extends CrudRequest
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
        if ($this->isMethod('put') || $this->isMethod('patch')) {
            return [
                'user_id' => [
                    'sometimes',
                    'required',
                    'exists:users,id',
                    Rule::unique('teachers', 'user_id')->ignore($this->routeId()),
                ],
                'teacher_id' => [
                    'sometimes',
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('teachers', 'teacher_id')->ignore($this->routeId()),
                ],
                'first_name' => ['sometimes', 'required', 'string', 'max:255'],
                'middle_name' => ['sometimes', 'nullable', 'string', 'max:255'],
                'last_name' => ['sometimes', 'required', 'string', 'max:255'],
                'department' => ['sometimes', 'nullable', 'string', 'max:255'],
                'status' => ['sometimes', 'required', 'in:active,inactive'],
            ];
        }

        return [];
    }

    public function messages(): array
    {
        return [
            'user_id.required' => 'The user field is required.',
            'user_id.exists' => 'The selected user does not exist.',
            'user_id.unique' => 'This user is already assigned as a teacher.',
            'teacher_id.required' => 'The teacher ID field is required.',
            'teacher_id.string' => 'The teacher ID must be a string.',
            'teacher_id.max' => 'The teacher ID may not be greater than 255 characters.',
            'teacher_id.unique' => 'The teacher ID already exists.',
            'first_name.required' => 'The first name field is required.',
            'first_name.string' => 'The first name must be a string.',
            'first_name.max' => 'The first name may not be greater than 255 characters.',
            'middle_name.string' => 'The middle name must be a string.',
            'middle_name.max' => 'The middle name may not be greater than 255 characters.',
            'last_name.required' => 'The last name field is required.',
            'last_name.string' => 'The last name must be a string.',
            'last_name.max' => 'The last name may not be greater than 255 characters.',
            'department.string' => 'The department must be a string.',
            'department.max' => 'The department may not be greater than 255 characters.',
            'status.required' => 'The status field is required.',
            'status.in' => 'The status must be either active or inactive.',
        ];
    }
}
