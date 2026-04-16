<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class ProgramsRequest extends CrudRequest
{
    public function rules(): array
    {

        if($this->isMethod('put') || $this->isMethod('patch')) {
            return [
                'name' => [
                    'sometimes',
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('programs', 'name')
                        ->where(fn ($query) => $query->where('department', $this->string('department')->toString()))
                        ->ignore($this->routeId()),
                ],
                'department' => ['sometimes', 'required', 'string', 'max:255'],
                'tuition_per_unit' => ['sometimes', 'required', 'numeric', 'min:0'],
            ];
        }
        return [];
    }
    public function messages(): array
    {
        return [
            'name.required' => 'The name field is required.',
            'name.unique' => 'The combination of name and department has already been taken.',
            'department.required' => 'The department field is required.',
            'tuition_per_unit.required' => 'The tuition per unit field is required.',
            'tuition_per_unit.numeric' => 'The tuition per unit must be a number.',
            'tuition_per_unit.min' => 'The tuition per unit must be at least 0.',
        ];
    }
}
