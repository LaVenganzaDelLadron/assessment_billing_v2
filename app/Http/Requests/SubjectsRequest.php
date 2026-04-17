<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class SubjectsRequest extends CrudRequest
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
                'code' => ['sometimes', 'required', 'string', 'max:255', Rule::unique('subjects', 'code')->ignore($this->routeId())],
                'name' => ['sometimes', 'required', 'string', 'max:255'],
                'units' => ['sometimes', 'required', 'numeric', 'min:0'],
                'program_id' => ['sometimes', 'required', 'exists:programs,id'],
            ];
        }
        return [];
    }
    public function messages(): array
    {
        return [
            'code.required' => 'The code field is required.',
            'code.unique' => 'The code has already been taken.',
            'name.required' => 'The name field is required.',
            'units.required' => 'The units field is required.',
            'units.numeric' => 'The units must be a number.',
            'units.min' => 'The units must be at least 0.',
            'program_id.required' => 'The program id field is required.',
            'program_id.exists' => 'The selected program id does not exist.',
        ];
    }
}
