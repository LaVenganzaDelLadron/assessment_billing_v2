<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class FeeStructureRequest extends CrudRequest
{
    public function rules(): array
    {
        return [
            'program_id' => ['required', 'exists:programs,id'],
            'fee_type' => [
                'required',
                'string',
                'max:255',
                Rule::unique('fee_structure', 'fee_type')
                    ->where(fn ($query) => $query->where('program_id', $this->string('program_id')->toString()))
                    ->ignore($this->routeId()),
            ],
            'amount' => ['required', 'numeric', 'min:0'],
            'per_unit' => ['required', 'boolean'],
        ];
    }
}
