<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class FeeStructureRequest extends CrudRequest
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
                'program_id' => ['sometimes', 'required', 'exists:programs,id'],
                'fee_type' => [
                    'sometimes',
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('fee_structure', 'fee_type')
                        ->where(fn ($query) => $query->where('program_id', $this->string('program_id')->toString()))
                        ->ignore($this->routeId()),
                ],
                'amount' => ['sometimes', 'required', 'numeric', 'min:0'],
                'per_unit' => ['sometimes', 'required', 'boolean'],
            ];
        }
        return [];
    }
    public function messages(): array
    {
        return [
            'program_id.exists' => 'The selected program does not exist.',
            'fee_type.required' => 'The fee type field is required.',
            'fee_type.unique' => 'The combination of program and fee type has already been taken.',
            'amount.required' => 'The amount field is required.',
            'amount.numeric' => 'The amount must be a number.',
            'amount.min' => 'The amount must be at least 0.',
            'per_unit.required' => 'The per unit field is required.',
            'per_unit.boolean' => 'The per unit must be a boolean.',
        ];
    }
}
